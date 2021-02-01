#!/usr/bin/python
# -*- coding: UTF-8 -*-

# import MySQLdb
import random
import re
import socket
import ConfigParser
import requests

if __name__ != "__main__":
    from mod_python import util
    from mod_python import apache
from BeautifulSoup import BeautifulSoup, Tag, NavigableString
import contextlib, errno, os, time

VALID_TAGS = ['br']

html_escape_table = {
    "&": "&amp;",
    '"': "&quot;",
    "'": "&apos;",
}
parser = ConfigParser.SafeConfigParser()

parser.read(os.path.dirname(os.path.abspath(__file__)) + '/' + socket.gethostname() + '.ini')

# dbuser = parser.get('db', 'user')
# # dbuser="hgkwmerchants"
# dbpasswd = parser.get('db', 'passwd')
# dbdb = parser.get('db', 'db')
# dbtable = parser.get('db', 'dbtable')
# gdusername = parser.get('gd', 'username')
# gdpasswd = parser.get('gd', 'passwd')
# dbtable_research = parser.get('db', 'dbtable_research')




def unescape(s):
    s = s.replace("&quot;", "\"")
    s = s.replace("&apos;", "'")
    # this has to be last:
    s = s.replace("&amp;", "&")
    return s


def html_escape(text):
    """Produce entities within text."""
    return "".join(html_escape_table.get(c, c) for c in text)


def sanitize_html(value):
    try:
        soup = BeautifulSoup(value)
        for tag in soup.findAll(True):
            #            print("Tag try: %s<br/><br/>"%tag)
            if tag.name not in VALID_TAGS:
                tag.hidden = True
        #        print("Returning try: %s"%soup.renderContents())
        return soup.renderContents()
    except:
        #        print("Returning except: %s"%value)
        return value;
        pass


def cleanupHTML(soup, language, left):
    combined = ''
    sections = []
    j = 0
    max = 0
    print('Processing language %s<br/>' % language)
    # tag = Tag(soup, "span")
    # soup.div.replaceWith(tag)
    for hr in soup.findAll('hr'):
        hr.extract()
    for table in soup.findAll('table'):
        table.extract()
    #         soup.append('<p>asdf</p>')
    #         print("soup=%s"%soup)
    # print(unicode.join(u'\n',map(unicode,soup.findAll('p'))))
    # print("length:%s"%(len(soup.findAll('p'))))
    number = ''
    for p in soup.findAll('h2'):
        try:
            #                 print('p=%s'%p)
            if (re.search(r'^\s*([\d]+)\.?\s*$', p.getText())):  # single number followed by dot -> paragraph number
                m = re.search(r'^\s*([\d]+)\.?\s*$', p.getText())
                #                     print(m.group(1))
                if (int(m.group(1)) > max):
                    max = int(m.group(1))
        except AttributeError:
            raise
    print('max %s =%s<br/>' % (language, max))
    for p in soup.findAll(lambda tag: tag.name == 'p' or tag.name == 'h2'):

        #             print("i cleanupHTML:%s<br/>"%j)
        #             print("p:%s<br/>"%p)
        #             j+=1
        # print('p=%s<br/>'%p)
        # print('p.getText=%s<br/>'%p.getText)
        try:
            # print('type:%s<br/>'%str(type(p)))

            if (p.getText() == ''):
                p.extract()
            elif (re.search(r'^\s*([\d]+)\.?\s*$', p.getText())):  # single number followed by dot -> paragraph number

                m = re.search(r'^\s*([\d]+)\.?\s*$', p.getText())
                number = int(m.group(1))
                if (language == left):
                    combined = re.sub(r'(Pagina|Page)\-([\d]+)',
                                      r'<span class="page_number" id="page_\g<2>">Page \g<2></span> ', combined)
                    # if(number!=max):
                    #                             print(number)
                    #                             print(max)
                    p.replaceWith("<div class='original_text'>" + combined + "</div><div id='paragraph_number_" + str(
                        number) + "_" + language + "'>" + p.getText() + "</div>")
                else:

                    # if(re.search(r'^\s*\&sect\;\s*([\d]+)\.?\s*$', p.getText())):
                    #    addtional_section_anchor="<a name='section_"+re.search(r'^\s*\&sect\;([\d]+)\.?\s*$', p.getText()).group(1)+">section</a>"

                    # p.replaceWith("<p>"+combined+"</p><div id='paragraph_number_"+m.group(1)+"_"+language+"'>"+p.getText()+"</div>")
                    # combined=re.sub(r'\&sect\;\s*([\d]+)\.?\s*',r'<span name="section_\g<1>"></span>&sect; \g<1>. ',combined)
                    combined = re.sub(r'(Pagina|Page)\-([\d]+)',
                                      r'<span class="page_number" name="page_\g<2>">Page \g<2></span> ', combined)
                    # print("%s<br/><br>"%m.group(1))
                    #                         if(m.group(1)=='0'):
                    #                             print('Processing special paragraph<br/><br/>')
                    #                             s=''
                    #                             for c in p.contents:
                    #                                 s += unicode(c)
                    #                             p.replaceWith("asdf"+combined+"<div id='paragraph_number_"+m.group(1)+"_"+language+"'>"+s+"</div>")
                    #                         else:
                    #                         if(number!=max):language
                    p.replaceWith("<div class='translation'><p>" + combined + "</p></div><div id='paragraph_number_" + str(
                            number) + "_" + language + "'>" + p.getText() + "</div>")
                combined = ''
            else:
                for i in p.contents:
                    # print('name=%s<br/>'%i)
                    for section in soup.findAll('p', text=re.compile(r'^&sect\;\s*([\d]+\w?)\.?\s*')):
                        text = re.compile(r'^&sect\;\s*([\d]+\w?)\.?\s*')
                        m = text.match(section)
                        if (m.group(1) == '0'):
                            # print("Special section %s"%m.group(1))
                            section = '<td width="50%%">%s</td>' % (re.sub(r'&sect\;\s*([\d]+\w?)\.?\s*', r'', section))
                        else:
                            section = '<td width="50%%"><a class="section_link" href="#section_%s">&sect; %s</a>. %s</td>' % (
                            m.group(1), m.group(1), re.sub(r'&sect\;\s*([\d]+\w?)\.?\s*', r'', section))
                        sections.append(section)
                        # print('Found section: %s<br/><br/>'%(section))

                    i = re.sub('-X-', '&nbsp;&nbsp;&nbsp;', unicode(i))
                    i = re.sub('-#--#-', '<span class="newline"></span>', i)
                    i = re.sub('-#-', '<br/>', i)
                    i = re.sub('<p></p>', '', i)
                    i = re.sub(r'\s*\&sect\;\s*(0+\w?)\.?\s*', r'', i)

                    combined += re.sub(r'^\s*(<[^>]+>)\s*\&sect\;\s*([^0][\d]*\w?)\.?\s*',
                                       r'\g<1><span name="section_\g<2>" id="section_\g<2>"></span>&sect; \g<2>. ',
                                       unicode(i))

                p.replaceWith('')

        except Exception as e:
            # print('Something went wrong processing the text, %s<br/>' % e.message)
            raise

    k = 0
    soup = BeautifulSoup("%s" % soup)
    # print('combined=%s'%combined)
    # print("\n\nsoup_before=%s<br/>\n"%soup)
    for d in soup.findAll("div"):
        k += 1

        # print('k=%s'%k)
        # print('len=%s'%len(soup.findAll('div')))
        if (k == len(soup.findAll('div'))):
            if (language == left):
                d.replaceWith("<div id='paragraph_number_" + str(number) + "_" + language + "'>" + str(
                    number) + ".</div><div class='original_text'>" + combined + "</div>")
            else:
                d.replaceWith("<div id='paragraph_number_" + str(number) + "_" + language + "'>" + str(
                    number) + ".</div><div class='translation'><p>" + combined + "</p></div>")
    soup = BeautifulSoup("%s" % soup)
    # print("\n\nsoup_after=%s<br/>\n"%soup)
    return (sections, soup, max)


def tocTranslation(language):
    if (language == "french"):
        return u"Table des matières"
    if (language == "english"):
        return u'Table of contents'
    if (language == "dutch"):
        return u'Inhoud'
    if (language == "test"):
        return u'Test'


def createTOC(sections_original, org_language, sections_translation, translation_language):
    toc = '<table id="toc_translation" width="100%" cellspacing="0" cellpadding="4"><colgroup> <col width="128*" /> <col width="128*" /> </colgroup><tbody>'
    toc += '<tr valign="TOP">'
    toc += '<td><div id="page-title">' + tocTranslation(org_language) + '</div></td>'
    toc += '<td><div id="page-title">' + tocTranslation(translation_language) + '</div></td>'
    toc += '</tr>'
    for i in range(min(len(sections_original), len(sections_translation))):
        toc += '<tr valign="TOP">' + sections_original[i] + sections_translation[i] + '</tr>'
    toc += '</tbody></table><h1></h1>'
    return toc


def return_string_until_paragraph(tn):
    s = ''
    while not (type(tn) is Tag and re.match(r'^\d+\.?$', str(tn.text))):
        if tn.nextSibling is None:
            break                
        s += str(tn)

        tn = tn.nextSibling
    return s

def createSideBySide(soup_org, language_org, soup_translation, language_translation, max_org, max_translation):
    side_by_side = '<table width="100%" cellspacing="0" cellpadding="4"><colgroup> <col width="128*" /> <col width="128*" /> </colgroup><tbody>'
    # print("\n\nsoup sbs=%s<br/>\n\n"%max_org)
    # print("\n\nsoup sbs=%s<br/>\n\n"%max_translation)

    if (max_org > max_translation):
        max = max_org
    else:
        max = max_translation
    # print("\n\nsoup sbs=%s<br/>\n\n"%max)
    for i in range(1, int(max) + 1):

        # print('i=%s<br/>'%i)
        paragraph_org = ''
        paragraph_translation = ''
        try:

            # print('finding %s %s<br/>'%(str(i),language_org))
            org_number = soup_org.find("div", id="paragraph_number_" + str(i) + "_" + language_org)
            paragraph_org = return_string_until_paragraph(org_number.nextSibling)
        except AttributeError as e:

            # print('<div class="error">No paragraph %s found for %s </div><br/>' % (i, language_org))

            raise
        try:
            translation_number = soup_translation.find("div",
                                                       id="paragraph_number_" + str(i) + "_" + language_translation)
            paragraph_translation = return_string_until_paragraph(translation_number.nextSibling)
        except AttributeError as e:
            print('<div class="error">No paragraph %s found for %s </div><br/>' % (i, language_translation))
            print('soup_translation='%soup_translation)
            raise
        # print("tag org=%s<br/>"%paragraph_org)
        # print("tag translation=%s<br/>"%paragraph_translation)
        side_by_side += '<tr valign="TOP"><td width="50%%"><a class="ref_nr" name="paragraph_%s" href="#paragraph_%s">%s</a>%s</td><td width="50%%"><a class="ref_nr" href="#paragraph_%s">%s</a>%s</td></tr>' % (
        i, i, i, paragraph_org, i, i, paragraph_translation)
    side_by_side += '</tbody></table>'
    return side_by_side


def getCSS(soup, replacement):
    try:
        css = soup.find("style")
        css_string = re.sub(r'}', "}\n" + replacement, css.string)
        css_string = re.sub(r'' + replacement + '$', '', css_string)
        # print("AFTER NEWLINES::<br/><br/>")
        # print(css_string);
        # css_string=re.sub(r'font-weight:bold;','font-weight:bold;display: block;padding-bottom:15px;margin-top:15px;',css_string)
        css_string = re.sub(r'font-weight:bold(?!;)',
                            'font-weight:bold;display: block;padding-bottom:15px;margin-top:15px;', css_string)
        css_string = re.sub(r'font-size:12pt(?!;)', '', css_string)
        css_string = re.sub(r'font-family:"[^"]+"(?!;)', '', css_string)
        # print("AFTER FONT WEIGHT BOLD::<br/><br/>")
        # print(css_string);

        subst = '<style type="text/css" media="all">#page_wrapper{font-size:10pt}' + css_string + "</style>"
    except Exception as e:
        print('<div class="error">%s</div>'%e.message)
        raise
    return subst


def createStandalone(soup_org, language_org, max_org):
    standalone = ''
    # print("\n\nsoup sbs=%s<br/>\n\n"%max_org)
    # print("\n\nsoup sbs=%s<br/>\n\n"%max_translation)

    # print("\n\nsoup sbs=%s<br/>\n\n"%max)
    for i in range(1, int(max_org) + 1):

        # print('i=%s<br/>'%i)
        paragraph_org = ''
        paragraph_translation = ''
        try:

            # print('finding %s %s<br/>'%(str(i),language_org))
            org_number = soup_org.find("div", id="paragraph_number_" + str(i) + "_" + language_org)
            # id_div="paragraph_number_"+str(i)+"_"+language_org
            # org_number= soup_org.find("div",{id:"paragraph_number_0_dutch"})
            # print('incoming soup_org=%s'%soup_org)
            # print('org_number: %s'%org_number)
            paragraph_org = org_number.nextSibling

            # if(i==530):
            # print("%s next sibling:%s<br/>"%(i,paragraph_org.getText()))
            # print(re.match(r'\d+\.?',paragraph_org.getText()))

            # failsafe if a paragraph number is the next sibling
            if (re.match(r'^\d+\.?$', paragraph_org.getText())):
                # print('matched org!')
                paragraph_org = paragraph_org.nextSibling
                # print("next sibling:%s<br/>"%paragraph_org)
            # print("tag org=%s<br/>"%paragraph_org)
        except AttributeError as e:

            print('<div class="error">No paragraph %s found for %s </div><br/>' % (i, language_org))

            # raise
            # print('soup_translation='%soup_translation)
            # raise
        # print("tag org=%s<br/>"%paragraph_org)
        # print("tag tranlation=%s<br/>"%paragraph_translation)
        # if(paragraph_org is not None):
        # paragraph_org=re.sub('-X-','&nbsp;&nbsp;&nbsp;','%s'%paragraph_org)
        # regex=re.compile(r'{([^}]+)}')
        # paragraph_org=re.sub(r'\{([^})\}','\1',paragraph_org)
        if (i == 0):
            i_show = ''
        else:
            i_show = i
        standalone += '<span class="ref_nr_standalone"><a name="paragraph_%s" href="#paragraph_%s">%s</a></span>%s' % (
        i, i, i_show, paragraph_org)
    return standalone


def handler(req, merchant=None):
    try:
        form = util.FieldStorage(req, keep_blank_values=1)
        org_language = form.get("org", None)
        translation_language = form.get('translation', None)
        req.log_error('handler')
        req.content_type = 'text/html'
        if (form.get("org", None) == None or form.get("translation", None) == None):
            raise Exception(
                'no org and translation parameter, use /translationHandler.py?org=dutch&translation=english for example')

        print(
            '<html><head>  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>Downloading feeds</title><link rel="stylesheet" href="script.css" type="text/css"></head><body>')
        print(
            "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>")
        req.flush()
        # print(os.path.dirname(os.path.realpath(__file__)))

        left = org_language
        right = translation_language

        left_path = os.path.dirname(os.path.realpath(__file__)) + '/' + left + '.html'
        right_path = os.path.dirname(os.path.realpath(__file__)) + '/' + right + '.html'

        left_url = "https://docs.google.com/feeds/download/documents/export/Export?id=" + parser.get('docs',
                                                                                                     left) + "&exportFormat=html"
        right_url = "https://docs.google.com/feeds/download/documents/export/Export?id=" + parser.get('docs',
                                                                                                      right) + "&exportFormat=html"

        print(
            'Downloading {}'.format(left))
        r = requests.get(left_url)
        with open(left_path, 'wb') as f:
            f.write(r.content)
        print(
            'Downloading {} as {}'.format(right, right_path))
        r = requests.get(right_url)
        if r.status_code == 200:
            with open(right_path, 'wb') as f:
                f.write(r.content)
        else:
            print(
                'Downloading failed {} as {}'.format(right, right_path))

        req.flush()
        f_original = open(left_path, 'rw')
        f_translation =open(right_path, 'rw')


        html_original = f_original.read()
        html_translation = f_translation.read()
        soup_original = BeautifulSoup(html_original)
        soup_translation = BeautifulSoup(html_translation)
        (sections_translation, soup_translation, max_translation) = cleanupHTML(soup_translation, translation_language,
                                                                                org_language)
        (sections_original, soup_original, max_org) = cleanupHTML(soup_original, org_language, org_language)
        soup_original = BeautifulSoup("%s" % soup_original)

        soup_original.prettify()
        soup_translation.prettify()
        soup_original = BeautifulSoup("%s" % soup_original)
        soup_translation = BeautifulSoup("%s" % soup_translation)
        side_by_side = getCSS(soup_original, '.original_text span')

        side_by_side += getCSS(soup_translation, '.translation span')
        org_number = soup_original.find("div", id="paragraph_number_" + str(0) + "_" + org_language)
        paragraph_org = org_number.nextSibling
        # print('paragraph_org={}'.format(paragraph_org))
        translation_number = soup_translation.find("div", id="paragraph_number_" + str(0) + "_" + translation_language)

        tn = translation_number.nextSibling
        # print('tn={}'.format(tn.contents))
        s = ''
        for p in tn.findAll('p'):
            for c in p.contents:
                s += unicode(c)
        tn.replaceWith(s)

        side_by_side += '<table width="100%" cellspacing="0" cellpadding="4"><colgroup> <col width="128*" /> <col width="128*" /> </colgroup><tbody>'
        side_by_side += '<tr valign="TOP"><td width="50%%"><div class="original_text">%s</div></td><td width="50%%"><div class="translation">%s</div></td>' % (
        paragraph_org, s)
        side_by_side += '</tbody></table>'
        if (form.get("skiptoc", None) == None):
            side_by_side += createTOC(sections_original, org_language, sections_translation, translation_language)
        # print('html_dutch = %s' % soup_original)

        side_by_side += createSideBySide(soup_original, org_language, soup_translation, translation_language, max_org,
                                         max_translation)
        # db = MySQLdb.connect(host="localhost", user=dbuser, passwd=dbpasswd, db=dbdb, charset="utf8", use_unicode=True)
        # cursor = db.cursor()
        # sql = """update """ + dbtable + """ set post_content=%s where post_name='""" + org_language + """-""" + translation_language + """'"""
        # sql = "update "+dbtable+" set post_content="+side_by_side+" where id=80"
        # print("%s <br/>"% sql)
        # print('Updating post in Wordpress...<br/>')
        # try:
            # Execute the SQL command
            # cursor.execute(sql, side_by_side)
        # except Exception as e:
            # print(e.message)
        try:
            print("%s<br/><br/>" % os.path.dirname(os.path.realpath(__file__)))
            os.remove(os.path.dirname(os.path.realpath(
                __file__)) + '/../../cake/app/tmp/cache/cake_' + org_language + '-' + translation_language)
            print('removed cache file<br/><br/>')
        except Exception as e:
            print('Could not remove cache file, not present<br/>\n')
            pass

        # standalone article

        update_standalone = form.get("update_standalone", None)
        if (update_standalone != None):
            if (org_language == update_standalone):
                try:
                    print('updating stand alone article from original %s<br/><br/>' % update_standalone)
                    standalone = getCSS(soup_original, '.original_text span')
                    standalone += createStandalone(soup_original, org_language, max_org)
                    # Execute the SQL command
                    # sql_standalone = """update """ + dbtable_research + """ set post_content=%s where post_name='""" + org_language + """'"""
                    # cursor.execute(sql_standalone, standalone)
                    # Commit your changes in the database
                    # db.commit()

                except Exception as e:
                    # print(e)
                    raise
            else:
                if (translation_language == update_standalone):
                    try:
                        print('updating stand alone article from translation %s<br/><br/>' % update_standalone)
                        standalone = getCSS(soup_translation, '.translation span')
                        standalone += createStandalone(soup_translation, translation_language, max_translation)
                        # Execute the SQL command
                        # sql_standalone = """update """ + dbtable_research + """ set post_content=%s where post_name='""" + translation_language + """'"""
                        # cursor.execute(sql_standalone, standalone)
                        # Commit your changes in the database
                        # db.commit()
                    except Exception as e:
                        print(e)
                        raise
            # print('sql = %s<br/>' % sql_standalone)
            print('sbs = %s<br/>' % standalone)
        # disconnect from server
        # db.close()


        print('side_by_side = %s' % side_by_side.encode('utf-8'))
        # print('html_dutch = %s' % soup_dutch)
        # print('html_english = %s' % html_english)
        print('Done<br/>')
        print('</body></html>')
    except Exception as e:
        print("Exception main handler: %s" % e.message)
        raise
    # return apache.OK


if __name__ == "__main__":

    # connect to Google drive
    # left = 'w365-2'
    # right = 'w365-2'
    left = 'left2'
    right = 'right2'
    # left_url = "https://docs.google.com/feeds/download/documents/export/Export?id=" + parser.get('docs',left) + "&exportFormat=html"
    # right_url = "https://docs.google.com/feeds/download/documents/export/Export?id=" + parser.get('docs',right)+ "&exportFormat=html"
    left_path = os.path.dirname(os.path.realpath(__file__)) + '/' + left + '.html'
    right_path = os.path.dirname(os.path.realpath(__file__)) + '/' + right + '.html'

    # r = requests.get(left_url, stream=True)
    # with open(left_path, 'wb') as f:
        # f.write(r.content)
    # r = requests.get(right_url, stream=True)
    # with open(right_path, 'wb') as f:
        # f.write(r.content)

    f_original = open(left_path, 'rw')
    f_translation =open(right_path, 'rw')

    html_original = f_original.read()
    html_translation = f_translation.read()
    soup_original0 = BeautifulSoup(html_original)
    soup_translation0 = BeautifulSoup(html_translation)
    (sections_translation, soup_translation1, max_translation) = cleanupHTML(soup_translation0, right,
                                                                            left)
    (sections_original, soup_original1, max_org) = cleanupHTML(soup_original0, left, left)
    soup_original2 = BeautifulSoup("%s" % soup_original1)
    soup_translation2 = BeautifulSoup("%s" % soup_translation1)

    soup_original2.prettify()
    soup_translation2.prettify()
    soup_original3 = BeautifulSoup("%s" % soup_original2)
    soup_translation3 = BeautifulSoup("%s" % soup_translation2)
    # side_by_side = getCSS(soup_original, '.original_text span')
    #
    # side_by_side += getCSS(soup_translation, '.translation span')
    side_by_side=''
    org_number = soup_original3.find("div", id="paragraph_number_" + str(0) + "_" + left)
    if org_number is not None: # missing 0. section
        paragraph_org = org_number.nextSibling
        translation_number = soup_translation3.find("div", id="paragraph_number_" + str(0) + "_" + right)
        tn = translation_number.nextSibling
        s = ''
        for p in tn.findAll('p'):
            for c in p.contents:
                s += unicode(c)
        tn.replaceWith(s)

        side_by_side += '<table width="100%" cellspacing="0" cellpadding="4"><colgroup> <col width="128*" /> <col width="128*" /> </colgroup><tbody>'
        side_by_side += '<tr valign="TOP"><td width="50%%"><div class="original_text">%s</div></td><td width="50%%"><div class="translation">%s</div></td>' % (
        paragraph_org, s)
        side_by_side += '</tbody></table>'
    if len(sections_original) != 0:
        side_by_side += createTOC(sections_original, left, sections_translation, right)
    # print('html_dutch = %s' % soup_original)

    side_by_side += createSideBySide(soup_original3, left, soup_translation3, right, max_org,
                                     max_translation)
    print(side_by_side)
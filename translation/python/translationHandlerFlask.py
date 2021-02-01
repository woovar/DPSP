#!/usr/bin/python
# -*- coding: UTF-8 -*-

# import MySQLdb
import random
import re
import socket
import ConfigParser
import requests
from HTMLParser import HTMLParser

from BeautifulSoup import BeautifulSoup, Tag, NavigableString
import contextlib, errno, os, time

VALID_TAGS = ['br']

html_escape_table = {
    "&": "&amp;",
    '"': "&quot;",
    "'": "&apos;",
}
upload_dir='/tmp'
parser = ConfigParser.SafeConfigParser()

parser.read(os.path.dirname(os.path.abspath(__file__)) + '/' + socket.gethostname() + '.ini')

dbuser = parser.get('db', 'user')
dbpasswd = parser.get('db', 'passwd')
dbdb = parser.get('db', 'db')
dbtable = parser.get('db', 'dbtable')
# gdusername = parser.get('gd', 'username')
# gdpasswd = parser.get('gd', 'passwd')
dbtable_research = parser.get('db', 'dbtable_research')




def unescape(s):
    s = s.replace("&quot;", "\"")
    s = s.replace("&apos;", "'")
    # this has to be last:
    s = s.replace("&amp;", "&")
    return s


def html_escape(text):
    """Produce entities within text."""
    return "".join(html_escape_table.get(c, c) for c in text)



def cleanupHTML(soup, language, left, skip):
    combined = ''
    sections = []
    j = 0
    max = 0
    print('Processing language %s<br/>' % language)
    for hr in soup.findAll('hr'):
        hr.extract()
    # for hr in soup.findAll('span'):
    #     hr.extract()
    # for f in soup.findAll('font',{'size': re.compile(r'\d')}):
    #     # f.replaceWith(BeautifulSoup(''.join([str(x) for x in f.contents])))
    #     # f.replaceWith(f.contents)
    #     f.extract()
    for table in soup.findAll('table'):
        table.extract()
    #         soup.append('<p>asdf</p>')
    #         print("soup=%s"%soup)
    # print(unicode.join(u'\n',map(unicode,soup.findAll('p'))))
    # print("length:%s"%(len(soup.findAll('p'))))
    number = ''
    for p in soup.findAll('p'):
        try:
            #                 print('p=%s'%p)
            if (re.search(r'^\s*([\d]+)\.?\s*$', p.getText())):  # single number followed by dot -> paragraph number
                m = re.search(r'^\s*([\d]+)\.?\s*$', p.getText())
                #                     print(m.group(1))
                if (int(m.group(1)) > max):
                    max = int(m.group(1))
        except AttributeError as e:
            return '<div class="error">%s</div>'%e.message
    print('max %s =%s<br/>' % (language, max))
    text = re.compile(r'^[\r\n]*&sect\;\s*([\d\.]+\w?)\.?\s*')

    for section in soup.findAll('h1'):
        section_to_add = section.getText()
        # m = text.match(section)
        # if m is None:
        #     section_new = u'&sect;'+''.join([x.getText() for x in section.parent.findNextSiblings()])
        #     if section_new == '&sect;':
        #         section_new =  u'&sect;'+''.join([x for x in section.next.getText()])
        #     if section_new == '&sect;':
        #         section_new =  ''.join([x for x in section.next.parent.getText()])
        #     if section_new != '&sect;':
        #         section=section_new
        #     # section = '<td width="50%%"><a class="section_link" href="#section_%s">&sect; %s</a>. %s</td>' % (
        m = text.match(section_to_add)
        try:
            if (m.group(1) == '0') or (m.group(1) == '0.') or'&sect; 0' in section_to_add.encode('utf8') or '§ 0' in section_to_add.encode('utf8'):
                # print("Special section %s"%m.group(1))
                section_to_add = re.sub(r'(?:§|&sect\;)\s*0\.?',r'',section_to_add)
                
                section_to_add = '<td width="50%%">%s</td>' % (re.sub(r'(?:§|&sect\;)\s*([\d\.]+\w?)\.?\s*', r'', section_to_add))
            else:
                section_to_add = '<td width="50%%"><a class="section_link" href="#section_%s">&sect; %s</a>. %s</td>' % (
                re.sub('\.$','',m.group(1)), re.sub('\.$','',m.group(1)), re.sub(r'&sect\;\s*([\d\.]+)\.?\w?','', section_to_add))
            # section_to_add=re.sub('\.\.','.',section_to_add)
    
            sections.append(section_to_add)
            # replacable=section.getText()
            # replacable=re.sub(r'(\d?\.?\d+)\.','\g<1>',replacable)
            section.replaceWith(re.sub(r'(?:§|&sect\;)\s*0\.?',r'',re.sub(
                        r'^\s*\&sect\;\s*([\d\.]*\.?\w?)\.\s*([^<]*)',
                        r'<span name="section_\g<1>" id="section_\g<1>"></span><h1>&sect; \g<1> \g<2></h1>',section.getText())))
        except AttributeError as e:
            print('Could not find matching group for section: {} not found {}'.format(section_to_add.encode('utf8'),e))
            pass
    length_p = len(soup.findAll('p'))
    for p in soup.findAll(['p','blockquote']):

        # print("i cleanupHTML:%s<br/>"%j)
        # print("p:%s<br/>"%p)
        j+=1
        if skip:
            if int(skip) > j:
                continue
        # print('p=%s<br/>'%p)
        # print('p.getText=%s<br/>'%p.getText)
        try:
            # print('type:%s<br/>'%str(type(p)))

            if (p.getText() == ''):
                p.extract()
            elif (re.search(r'^\s*([\d]+)\.?(\s|&.*?;)*$', p.getText())):  # single number followed by dot -> paragraph number

                m = re.search(r'^\s*([\d]+)\.?(\s|&.*?;)*$', p.getText())
                number = int(m.group(1))
                if (language == left):
                    combined = re.sub(r'(Pagina|Page)\-([\d]+)',
                                      r'<span class="page_number" id="page_\g<2>">Page \g<2></span> ', combined)
                    # if(number!=max):
                    #                             print(number)
                    #                             print(max)
                    p.replaceWith("<div class='original_text'><p>" + combined + "</p></div><div id='paragraph_number_" + str(
                        number) + "_" + language + "'>" + str(p) + "</div>")
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
                            number) + "_" + language + "'>" + str(p) + "</div>")
                combined = ' '
            else:

                # print('Found section: %s<br/><br/>'%(section))
                if p.name=='blockquote':
                    combined+='<blockquote>'
                for i in p.contents:
                    # print('name=%s<br/>'%i)
                    i=unicode(i)
                    # i = re.sub(r'<br\s?.?>', '', unicode(i))
                    # i = re.sub(r'<BR\s?.?>', '', i)
                    i = re.sub('-X-', '&nbsp;&nbsp;&nbsp;', i)
                    i = re.sub('-#--#-', '<span class="newline"></span>', i)
                    i = re.sub('-#-', '<br>', i)
                    # i = re.sub('-#-', '<br/>', i)
                    i = re.sub('<p></p>', ' ', i)
                    # i = re.sub('##1(.*?)--1', '<div style="color: green;">\g<1></div>', i)
                    # i = re.sub('##2(.*?)--2', '<div style="color: blue;">\g<1></div>', i)
                    # i = re.sub('##3(.*?)--3', '<div style="color: purple;">\g<1></div>', i)
                    
                    # i = re.sub(r'\s*(&sect\;|§)\s*(0+\w?)\.?\s*', r'', i.encode('utf8'))
                    
                    combined += re.sub(
                        r'^\s*(<[^>]+>)\s*\&sect\;\s*([^0][\d]*\w?)\.?\s*',
                        r'\g<1><span name="section_\g<2>" id="section_\g<2>"></span>&sect; \g<2>. ',
                                        unicode(i))
                    
                p.replaceWith('')
                if p.name=='blockquote':
                    combined+='</blockquote>'

        except Exception as e:
            # print('Something went wrong processing the text, %s<br/>' % e.message)
            raise
    # if (language == left):
    #     p.replaceWith("<div class='original_text'>" + combined + "</div><div id='paragraph_number_" + str(
    #                     number) + "_" + language + "'>" + str(p) + "</div>")
    # else:
    #     p.replaceWith("<div class='translation'><p>" + combined + "</p></div><div id='paragraph_number_" + str(
    #                         number) + "_" + language + "'>" + str(p) + "</div>")

    k = 0
    # soup_n = BeautifulSoup("%s" % soup)
    # print('combined=%s'%combined)
    # print("\n\nsoup_before=%s<br/>\n"%soup)
    length = len(soup.findAll('div'))
    for d in soup.findAll("div"):
        k += 1

        # print('k=%s'%k)
        # print('len=%s'%len(soup.findAll('div')))
        if (k == length):
            if (language == left):
                d.replaceWith(BeautifulSoup(combined))
                # d.replaceWith("<div id='paragraph_number_" + str(number) + "_" + language + "'>" + str(
                #     number) + ".</div><div class='original_text'>" + combined + "</div>")
            else:
                d.replaceWith(BeautifulSoup(combined))
                # d.replaceWith("<div id='paragraph_number_" + str(number) + "_" + language + "'>" + str(
                #     number) + ".</div><div class='translation'><p>" + combined + "</p></div>")
    soup = BeautifulSoup("%s" % soup)
    # print("\n\nsoup_after=%s<br/>\n"%soup)
    return (sections, soup, max)



def tocTranslation(language):
    if (re.search(r'french',language)):
        return u"Table des matières"
    if (re.search(r'english',language)):
        return u'Table of contents'
    if (re.search(r'dutch',language)):
        return u'Inhoud'
    if (re.search(r'indonesian',language)):
        return u'Daftar isi'
    if (re.search(r'test',language)):
        return u'Test'
    else:
        return u'Table of contents (not translated)'

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
    # while not (not '<blockquote' in str(tn) and type(tn) is Tag and re.match(r'^\d+\.?$', str(tn.text.encode('utf8')))):
    while not (type(tn) is Tag and re.match(r'^\d+\.?$', str(tn.text.encode('utf8')))):
        if tn is None or tn is None:
            break                
        s += str(tn)

        tn = tn.nextSibling
    return s.replace('\r\n',' ')

def createSideBySide(soup_org, language_org, soup_translation, language_translation, max_org, max_translation, skip):
    side_by_side = '<table width="100%" cellspacing="0" cellpadding="4"><colgroup> <col width="128*" /> <col width="128*" /> </colgroup><tbody>'
    # print("\n\nsoup sbs=%s<br/>\n\n"%max_org)
    # print("\n\nsoup sbs=%s<br/>\n\n"%max_translation)

    if (max_org > max_translation):
        max = max_org
    else:
        max = max_translation
    # print("\n\nsoup sbs=%s<br/>\n\n"%max)
    for i in range(1, int(max) + 1):
        if skip:
            if int(skip) > i:
                continue
        # print('i=%s<br/>'%i)
        paragraph_org = ''
        paragraph_translation = ''
        try:

            # print('finding %s %s<br/>'%(str(i),language_org))
            org_number = soup_org.find("div", id="paragraph_number_" + str(i) + "_" + language_org)
            if i==max:
    # html_original= p.sub('', html_original)
                paragraph_org = re.sub(r'<font size="?\d"?>([^\]]*?)</font>',r'\1',re.sub(r'<b>\d+\.?</b>','',''.join([str(x) for x in org_number.findNextSiblings()])))
                # paragraph_org = re.sub(r'<font size="?\d"?>[^\n]</font>','\1',re.sub(r'<b>\d+\.?</b>','',''.join([str(x) for x in org_number.findNextSiblings()])))
            else:
                paragraph_org = return_string_until_paragraph(org_number.nextSibling)
        except AttributeError as e:

            print('<div class="error">No paragraph %s found for %s </div><br/>' % (i, language_org))
            return '<div class="error">No paragraph %s found for %s </div><br/>' % (i, language_org)
        try:
            translation_number = soup_translation.find("div",
                                                       id="paragraph_number_" + str(i) + "_" + language_translation)
            if i==max:
                # paragraph_translation = re.sub(r'<b>\d+\.?</b>','',''.join([str(x) for x in translation_number.findNextSiblings()]))
                paragraph_translation = re.sub(r'<font size="?\d"?>([^\]]*?)</font>',r'\1',re.sub(r'<b>\d+\.?</b>','',''.join([str(x) for x in translation_number.findNextSiblings()])))
            else:
                paragraph_translation = return_string_until_paragraph(translation_number.nextSibling)
        except AttributeError as e:
            print('<div class="error">No paragraph %s found for %s </div><br/>' % (i, language_translation))
            print('soup_translation='%soup_translation)
            return '<div class="error">No paragraph %s found for %s </div><br/>' % (i, language_translation)
        # print("tag org=%s<br/>"%paragraph_org)
        # print("tag translation=%s<br/>"%paragraph_translation)
        side_by_side += '<tr valign="TOP"><td width="50%%"><a class="ref_nr" name="paragraph_%s" href="#paragraph_%s">%s</a>%s</td><td width="50%%"><a class="ref_nr" href="#paragraph_%s">%s</a>%s</td></tr>' % (
        i, i, i, paragraph_org, i, i, paragraph_translation)
    side_by_side += '</tbody></table>'
    return side_by_side


def getCSS(soup, replacement):
    try:
        css = soup.find("style")
        css_string = re.sub(r'\}', "}\n" + replacement, str(css))
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
        return '<div class="error">%s</div>'%e.message
    return subst


def createStandalone(soup_org, language_org, max_org,skip):
    standalone = ''
    # print("\n\nsoup sbs=%s<br/>\n\n"%max_org)
    # print("\n\nsoup sbs=%s<br/>\n\n"%max_translation)

    # print("\n\nsoup sbs=%s<br/>\n\n"%max)
    for i in range(1, int(max_org) + 1):
        if skip:
            if int(skip) > i:
                continue
        # print('i=%s<br/>'%i)
        paragraph_org = ''
        paragraph_translation = ''
        try:

            # print('finding %s %s<br/>'%(str(i),language_org))
            org_number = soup_org.find("div", id="paragraph_number_" + str(i) + "_" + language_org)
            if i==max_org:
    # html_original= p.sub('', html_original)
                paragraph_org = re.sub(r'<font size="?\d"?>([^\]]*?)</font>',r'\1',re.sub(r'<b>\d+\.?</b>','',''.join([str(x) for x in org_number.findNextSiblings()])))
                # paragraph_org = re.sub(r'<font size="?\d"?>[^\n]</font>','\1',re.sub(r'<b>\d+\.?</b>','',''.join([str(x) for x in org_number.findNextSiblings()])))
            else:
                paragraph_org = return_string_until_paragraph(org_number.nextSibling)
            pass
        except AttributeError as e:


            print('<div class="error">No paragraph %s found for %s </div><br/>' % (i, language_org))
            # return '<div class="error">No paragraph %s found for %s </div><br/>' % (i, language_org)

            # raise
            # print('soup_translation='%soup_translation)
            # raise
        # print("tag org=%s<br/>"%paragraph_org)
        # print("tag tranlation=%s<br/>"%paragraph_translation)
        # if(paragraph_org is not None):
        # paragraph_org=re.sub('-X-','&nbsp;&nbsp;&nbsp;','%s'%paragraph_org)
        # regex=re.compile(r'{([^}]+)}')
        # paragraph_org=re.sub(r'\{([^})\}','\1',paragraph_org)
        # if paragraph_org.startswith('\n<blockquote>'):
            # paragraph_org = re.sub(r'<blockquote>((?:\n|.)+)<\/blockquote>(?:\n*)<div((?:\n|.)*)</div>',r'<div\2 <blockquote>\1</blockquote></div>',paragraph_org)
        if (i == 0):
            i_show = ''
        else:
            i_show = i
        standalone += '<span class="ref_nr_standalone"><a name="paragraph_%s" href="#paragraph_%s">%s</a></span>%s' % (
        i, i, i_show, paragraph_org)
    return standalone


# def handler(req, merchant=None):
#     try:
#         form = util.FieldStorage(req, keep_blank_values=1)
#         org_language = form.get("org", None)
#         translation_language = form.get('translation', None)
#         req.log_error('handler')
#         req.content_type = 'text/html'
#         if (form.get("org", None) == None or form.get("translation", None) == None):
#             raise Exception(
#                 'no org and translation parameter, use /translationHandler.py?org=dutch&translation=english for example')

#         print(
#             '<html><head>  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>Downloading feeds</title><link rel="stylesheet" href="script.css" type="text/css"></head><body>')
#         print(
#             "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>")
#         req.flush()
#         # print(os.path.dirname(os.path.realpath(__file__)))

#         left = org_language
#         right = translation_language

#         left_path = os.path.dirname(os.path.realpath(__file__)) + '/' + left + '.html'
#         right_path = os.path.dirname(os.path.realpath(__file__)) + '/' + right + '.html'

#         left_url = "https://docs.google.com/feeds/download/documents/export/Export?id=" + parser.get('docs',
#                                                                                                      left) + "&exportFormat=html"
#         right_url = "https://docs.google.com/feeds/download/documents/export/Export?id=" + parser.get('docs',
#                                                                                                       right) + "&exportFormat=html"

#         print(
#             'Downloading {}'.format(left))
#         r = requests.get(left_url)
#         with open(left_path, 'wb') as f:
#             f.write(r.content)
#         print(
#             'Downloading {} as {}'.format(right, right_path))
#         r = requests.get(right_url)
#         if r.status_code == 200:
#             with open(right_path, 'wb') as f:
#                 f.write(r.content)
#         else:
#             print(
#                 'Downloading failed {} as {}'.format(right, right_path))

#         req.flush()
#         f_original = open(left_path, 'rw')
#         f_translation =open(right_path, 'rw')


#         html_original = f_original.read()
#         html_translation = f_translation.read()
#         soup_original = BeautifulSoup(html_original)
#         soup_translation = BeautifulSoup(html_translation)
#         (sections_translation, soup_translation, max_translation) = cleanupHTML(soup_translation, translation_language,
#                                                                                 org_language)
#         (sections_original, soup_original, max_org) = cleanupHTML(soup_original, org_language, org_language)
#         soup_original = BeautifulSoup("%s" % soup_original)

#         soup_original.prettify()
#         soup_translation.prettify()
#         soup_original = BeautifulSoup("%s" % soup_original)
#         soup_translation = BeautifulSoup("%s" % soup_translation)
#         # side_by_side = getCSS(soup_original, '.original_text span')

#         # side_by_side += getCSS(soup_translation, '.translation span')
#         org_number = soup_original.find("div", id="paragraph_number_" + str(0) + "_" + org_language)
#         paragraph_org = org_number.nextSibling
#         # print('paragraph_org={}'.format(paragraph_org))
#         translation_number = soup_translation.find("div", id="paragraph_number_" + str(0) + "_" + translation_language)

#         tn = translation_number.nextSibling
#         # print('tn={}'.format(tn.contents))
#         s = ''
#         for p in tn.findAll('p'):
#             for c in p.contents:
#                 s += unicode(c)
#         tn.replaceWith(s)

#         side_by_side += '<table width="100%" cellspacing="0" cellpadding="4"><colgroup> <col width="128*" /> <col width="128*" /> </colgroup><tbody>'
#         side_by_side += '<tr valign="TOP"><td width="50%%"><div class="original_text">%s</div></td><td width="50%%"><div class="translation">%s</div></td>' % (
#         paragraph_org, s)
#         side_by_side += '</tbody></table>'
#         if (form.get("skiptoc", None) == None):
#             side_by_side += createTOC(sections_original, org_language, sections_translation, translation_language)
#         # print('html_dutch = %s' % soup_original)

#         side_by_side += createSideBySide(soup_original, org_language, soup_translation, translation_language, max_org,
#                                          max_translation)
#         db = MySQLdb.connect(host="localhost", user=dbuser, passwd=dbpasswd, db=dbdb, charset="utf8", use_unicode=True)
#         cursor = db.cursor()
#         sql = """update """ + dbtable + """ set post_content=%s where post_name='""" + org_language + """-""" + translation_language + """'"""
#         # sql = "update "+dbtable+" set post_content="+side_by_side+" where id=80"
#         # print("%s <br/>"% sql)
#         print('Updating post in Wordpress...<br/>')
#         try:
#             # Execute the SQL command
#             cursor.execute(sql, side_by_side)
#         except Exception as e:
#             print(e.message)
#         try:
#             print("%s<br/><br/>" % os.path.dirname(os.path.realpath(__file__)))
#             os.remove(os.path.dirname(os.path.realpath(
#                 __file__)) + '/../../cake/app/tmp/cache/cake_' + org_language + '-' + translation_language)
#             print('removed cache file<br/><br/>')
#         except Exception as e:
#             print('Could not remove cache file, not present<br/>\n')
#             pass

#         # standalone article

#         update_standalone = form.get("update_standalone", None)
#         if (update_standalone != None):
#             if (org_language == update_standalone):
#                 try:
#                     print('updating stand alone article from original %s<br/><br/>' % update_standalone)
#                     standalone = getCSS(soup_original, '.original_text span')
#                     standalone += createStandalone(soup_original, org_language, max_org)
#                     # Execute the SQL command
#                     # sql_standalone = """update """ + dbtable_research + """ set post_content=%s where post_name='""" + org_language + """'"""
#                     # cursor.execute(sql_standalone, standalone)
#                     # Commit your changes in the database
#                     # db.commit()

#                 except Exception as e:
#                     # print(e)
#                     raise
#             else:
#                 if (translation_language == update_standalone):
#                     try:
#                         print('updating stand alone article from translation %s<br/><br/>' % update_standalone)
#                         standalone = getCSS(soup_translation, '.translation span')
#                         standalone += createStandalone(soup_translation, translation_language, max_translation)
#                         # Execute the SQL command
#                         # sql_standalone = """update """ + dbtable_research + """ set post_content=%s where post_name='""" + translation_language + """'"""
#                         # cursor.execute(sql_standalone, standalone)
#                         # Commit your changes in the database
#                         # db.commit()
#                     except Exception as e:
#                         print(e)
#                         raise
#             # print('sql = %s<br/>' % sql_standalone)
#             print('sbs = %s<br/>' % standalone)
#         # disconnect from server
#         # db.close()


#         print('side_by_side = %s' % side_by_side.encode('utf-8'))
#         # print('html_dutch = %s' % soup_dutch)
#         # print('html_english = %s' % html_english)
#         print('Done<br/>')
#         print('</body></html>')
#     except Exception as e:
#         print("Exception main handler: %s" % e.message)
#         raise
#     # return apache.OK


def go(left, right, slug, update_standalone=None, skip=None):
    # connect to Google drive
    # left = 'w365-2'
    # right = 'w365-2'
    # left_url = "https://docs.google.com/feeds/download/documents/export/Export?id=" + parser.get('docs',left) + "&exportFormat=html"
    # right_url = "https://docs.google.com/feeds/download/documents/export/Export?id=" + parser.get('docs',right)+ "&exportFormat=html"
    left_path = upload_dir + '/' + left 
    right_path = upload_dir + '/' + right

    # r = requests.get(left_url, stream=True)
    # with open(left_path, 'wb') as f:
        # f.write(r.content)
    # r = requests.get(right_url, stream=True)
    # with open(right_path, 'wb') as f:
        # f.write(r.content)

    f_original = open(left_path, 'rw')
    f_translation =open(right_path, 'rw')

    html_original = f_original.read().decode('utf8')
    html_translation = f_translation.read().decode('utf8')
    # p = re.compile(r'<font size="?\d"?>(.*?)</font>', flags=re.IGNORECASE)
    # html_original=re.sub(r'<FONT SIZE="?\d"?>([^\]]*?)</FONT>',r'\1', html_original)
    # html_original= p.sub('\g<1>', html_original)
    # html_original= p.sub('\g<1>', html_original)
    html_original = re.sub(r'<A NAME="_\w*"></A>','', html_original)
    html_translation = re.sub(r'<A NAME="_\w*"></A>','', html_translation)
    html_original = re.sub(r'<A HREF\="([\w\d\.\/\:\-]+\.jpg)">(\n|.)*?</A>',r'\1', html_original)
    html_translation = re.sub(r'<A HREF\="([\w\d\.\/\:\-]+\.jpg)">(\n|.)*?</A>',r'\1', html_translation)
    # html_translation=re.sub(r'<FONT SIZE="?\d"?>([^\]]*?)</FONT>',r'\1', html_translation)
    # html_translation= p.sub('\g<1>', html_translation)
    # html_translation= p.sub('\g<1>', html_translation)
    soup_original0 = BeautifulSoup(html_original, smartQuotesTo=False)



    soup_translation0 = BeautifulSoup(html_translation)



    (sections_original, soup_original1, max_org) = cleanupHTML(soup_original0, left, left,skip)
    (sections_translation, soup_translation1, max_translation) = cleanupHTML(soup_translation0, right, left,skip)
    h = HTMLParser()
    soup_original1=h.unescape(str(soup_original1).decode('utf8'))
    soup_translation1=h.unescape(str(soup_translation1).decode('utf8'))
    soup_original2 = BeautifulSoup("%s" % soup_original1)
    soup_translation2 = BeautifulSoup("%s" % soup_translation1)

    soup_original2.prettify()
    soup_translation2.prettify()
    soup_original_between = BeautifulSoup("%s" % soup_original2, smartQuotesTo=False)
    soup_translation3 = BeautifulSoup("%s" % soup_translation2, smartQuotesTo=False)
    soup_original3 = BeautifulSoup("%s" % soup_original_between)


    # side_by_side = getCSS(soup_original0, '.original_text span')
    # for f in soup_original_between.findAll('font'):
    #     f.replaceWithChildren()
    # for fo in soup_original_between.findAll('font'):
    #     fo.extract()
    # for fo in soup_original3.findAll('font'):
    #     fo.extract()
    # for f in soup_translation3.findAll('font'):
    #     f.replaceWithChildren()
    # side_by_side += getCSS(soup_translation0, '.translation span')
    side_by_side=''
    org_number = soup_original3.find("div", id="paragraph_number_" + str(0) + "_" + left)
    if org_number is not None: # 0. section
        on = org_number.nextSibling
        # if type(org_number)==str:
        #     on = on.nextSibling
        # if type(org_number)==NavigableString:
        #     on = on.nextSibling
        # if type(org_number)==str:
        #     on = on.nextSibling
        if type(on)==NavigableString:
            on = on.nextSibling
        o = ''
        for p in on.findAll('p'):
            for c in p.contents:
                o += unicode(c)
        if o=='':
            for p in on.findAll():
                # for c in p:
                o += unicode(p)            
        on.replaceWith(o)

        translation_number = soup_translation3.find("div", id="paragraph_number_" + str(0) + "_" + right)
        tn = translation_number.nextSibling
        s = ''
        if type(tn)==NavigableString:
            tn = tn.nextSibling
        for p in tn.findAll('p'):
            for c in p.contents:
                s += unicode(c)
        if s=='':
            for p in tn.findAll():
                # for c in p:
                s += unicode(p)    
        # for p in tn.findAll('p'):
        #     for c in p.contents:
        #         s += unicode(c)
        tn.replaceWith(s)

        side_by_side += '<table width="100%" cellspacing="0" cellpadding="4"><colgroup> <col width="128*" /> <col width="128*" /> </colgroup><tbody>'
        side_by_side += '<tr valign="TOP"><td width="50%%"><div class="original_text">%s</div></td><td width="50%%"><div class="translation">%s</div></td>' % (
        o, s)
        side_by_side += '</tbody></table>'
    if len(sections_original) != 0:
        side_by_side += createTOC(sections_original, left, sections_translation, right)
    # print('html_dutch = %s' % soup_original)


    # standalone article
    standalone=None
    if (update_standalone != None):
        if (left == update_standalone):
            try:
                print('updating stand alone article from original %s<br/><br/>' % update_standalone)
                standalone = getCSS(soup_original3, '.original_text span')
                standalone += createStandalone(soup_original3, left, max_org,skip).decode('utf8')
                # print(standalone)
                # Execute the SQL command
                # sql_standalone = """update """ + dbtable_research + """ set post_content=%s where post_name='""" + org_language + """'"""
                # cursor.execute(sql_standalone, standalone)
                # Commit your changes in the database
                # db.commit()

            except Exception as e:
                print(e)
                raise
        else:
            if (right == update_standalone):
                try:
                    print('updating stand alone article from translation %s<br/><br/>' % update_standalone)
                    standalone = getCSS(soup_translation3, '.translation span')
                    standalone += createStandalone(soup_translation3, right, max_translation,skip).decode('utf8')
                    # print(standalone)
                    # Execute the SQL command
                    # sql_standalone = """update """ + dbtable_research + """ set post_content=%s where post_name='""" + translation_language + """'"""
                    # cursor.execute(sql_standalone, standalone)
                    # Commit your changes in the database
                    # db.commit()
                    
                except Exception as e:
                    print(e)
                    raise
        standalone = re.sub(r'([\w\d\.\/\:\-]+\.jpg)',r'<a href="\1"><img src="\1"/></a>', standalone)
        return (standalone,standalone,standalone)             
        # print('sql = %s<br/>' % sql_standalone)
        # print('sbs = %s<br/>' % standalone)
    # disconnect from server
    # db.close()













    side_by_side += createSideBySide(soup_original3, left, soup_translation3, right, max_org,
                                     max_translation, skip).decode('utf8')

    side_by_side_html="""<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><link type="text/css" rel="stylesheet" href="https://paulscholten.eu/css/print.css" media="print" /><link type="text/css" rel="stylesheet" href="http://paulscholten.eu/css/override.css" media="screen" /></head><body>
    <div id="mainCntr">	
			<div id="contentCntr">
				<div id="textCntr">
					
<div id="container">
<div id="main_wrapper" class="clearfix">
<div id="page_wrapper">
	<div id="content" class="clearfix">
		<div class="post">
    
    """.decode('utf8')
    side_by_side_html += side_by_side
    side_by_side_html += '</div></div></div></div></div></div></div></div></body></html>'
    side_by_side_html = re.sub(r'([\w\d\.\/\:\-]+\.jpg)',r'<a href="\1"><img src="\1"/></a>', side_by_side_html)
    side_by_side = re.sub(r'([\w\d\.\/\:\-]+\.jpg)',r'<a href="\1"><img src="\1"/></a>', side_by_side)
    return (side_by_side, side_by_side_html, standalone)
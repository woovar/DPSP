#!/usr/bin/python
# -*- coding: UTF-8 -*-

import sys
import time
import gdata.client
import gdata.docs

import gdata.docs.service
import MySQLdb
import random
import HTMLParser
import re
import cookielib
import urllib2
import socket
import ConfigParser
import codecs


from mod_python import util
from mod_python import apache
from BeautifulSoup import BeautifulSoup
import contextlib, errno, os, time



VALID_TAGS = ['br']

html_escape_table = {
    "&": "&amp;",
    '"': "&quot;",
    "'": "&apos;",
    }
parser = ConfigParser.SafeConfigParser()

parser.read(os.path.dirname(os.path.abspath(__file__))+'/'+socket.gethostname()+'.ini')




dbuser=parser.get('db', 'user')
#dbuser="hgkwmerchants"
dbpasswd=parser.get('db', 'passwd')
dbdb=parser.get('db', 'db')
dbtable=parser.get('db', 'dbtable')
gdusername=parser.get('gd', 'username')
gdpasswd=parser.get('gd', 'passwd')
dbtable_research=parser.get('db', 'dbtable_research')


def GetResourcesWithExponentialBackoff(req,left,right,client):



  """Gets all of the resources for the user authorized on the given client.

  Args:
    client: gdata.docs.client.DocsClient authorized for a user.
  Returns:
    gdata.docs.data.ResourceFeed representing Resources found in the request.
  """
  req.write('Downloading texts...<br/><br/>')
  #req.write('Left = %s<br/><br/>'%left)
  #req.write('Right = %s<br/><br/>'%right)
  documents_feed = client.GetDocumentListFeed()
  #req.write("response=%s"%documents_feed)
  nr_found=0
  for document_entry in documents_feed.entry:
        # Display the title of the document on the command line.
        #req.write('entry=+%s+<br/>'%document_entry.title.text)

        n=1
        if(document_entry.title.text.strip()==left or document_entry.title.text.strip()==right):
            #req.write('Found %s<br/><br/>'%(document_entry.title.text))
            nr_found+=1
            #req.write('Downloading %s<br/>'%document_entry.title.text)
            try:
              #response = client.Export("https://docs.google.com/feeds/download/documents/export/Export?id=1jPIejiXEocDJLnUwDNa9--EA3FZW_8lItVEXea9W3Yw&exportFormat=html", os.path.dirname(os.path.realpath(__file__))+'/dutch.html')
              #response = client.Export("https://docs.google.com/feeds/download/documents/export/Export?id=1RSG6A5rakspG4-MR0I5QrASo2u5q5RhccaNF5QV7I54=html", os.path.dirname(os.path.realpath(__file__))+'/english.html')
              #req.write("content=%s<br/>"%document_entry.content.src+'&exportFormat=html')
              #req.write('saving to %s<br/>'%os.path.dirname(os.path.realpath(__file__))+'/'+document_entry.title.text+'.html')
              response=client.Export(document_entry.content.src+'&exportFormat=html',os.path.dirname(os.path.realpath(__file__))+'/'+document_entry.title.text.strip()+'.html')

              #req.write("response=%s<br/>" % response)
              #return response
            except gdata.client.RequestError, error:
              computed_time = (2 ** n) + (random.randint(0, 1000) / 1000)
              time.sleep(max(error.headers.get('Retry-After'), computed_time))
              raise
            except:
              time.sleep((2 ** n) + (random.randint(0, 1000) / 1000))
              raise
  if(nr_found!=2):
       req.write('could not download both texts from Google<br/><br/>')
       #raise Exception('Number of docs found not 2')

def unescape(s):
    s = s.replace("&quot;", "\"")
    s = s.replace("&apos;", "'")
    # this has to be last:
    s = s.replace("&amp;", "&")
    return s

def html_escape(text):
    """Produce entities within text."""
    return "".join(html_escape_table.get(c,c) for c in text)


def getFeedFromGoogle(client,req):

    GetResourcesWithExponentialBackoff(client)
    req.write('</body></html>')

def sanitize_html(value,req):
    try:
        soup = BeautifulSoup(value)
        for tag in soup.findAll(True):
#            req.write("Tag try: %s<br/><br/>"%tag)
            if tag.name not in VALID_TAGS:
                tag.hidden = True
#        req.write("Returning try: %s"%soup.renderContents())
        return soup.renderContents()
    except:
#        req.write("Returning except: %s"%value)
        return value;
        pass

def cleanupHTML(soup,language,left,req):
        combined=''
        sections=[]
        j=0
        max=0
        req.write('Processing language %s<br/>'%language)
        for hr in soup.findAll('hr'):
            hr.extract()
        for table in soup.findAll('table'):
            table.extract()
#         soup.append('<p>asdf</p>')
#         req.write("soup=%s"%soup)
        #req.write(unicode.join(u'\n',map(unicode,soup.findAll('p'))))
       #req.write("length:%s"%(len(soup.findAll('p'))))
        number=''
        for p in soup.findAll('p'):
            try:
#                 req.write('p=%s'%p)
                if(re.search(r'^\s*([\d]+)\.?\s*$', p.getText())):#single number followed by dot -> paragraph number
                    m=re.search(r'^\s*([\d]+)\.?\s*$', p.getText())
#                     req.write(m.group(1))
                    if(int(m.group(1))>max):
                        max=int(m.group(1))
            except AttributeError:
                raise     
        req.write('max %s =%s<br/>'%(language,max))
        for p in soup.findAll('p'):
                
#             req.write("i cleanupHTML:%s<br/>"%j)
#             req.write("p:%s<br/>"%p)
#             j+=1
            #req.write('p=%s<br/>'%p)
            #req.write('p.getText=%s<br/>'%p.getText)
            try:
                #req.write('type:%s<br/>'%str(type(p)))

                if(p.getText()==''):
                    p.extract()
                elif(re.search(r'^\s*([\d]+)\.?\s*$', p.getText())):#single number followed by dot -> paragraph number

                    m=re.search(r'^\s*([\d]+)\.?\s*$', p.getText())
                    number=int(m.group(1))
                    if(language==left):
                        combined=re.sub(r'(Pagina|Page)\-([\d]+)',r'<span class="page_number" id="page_\g<2>">Page \g<2></span> ',combined)
                        #if(number!=max):
#                             req.write(number)
#                             req.write(max)
                        p.replaceWith("<div class='original_text'>"+combined+"</div><div id='paragraph_number_"+str(number)+"_"+language+"'>"+p.getText()+"</div>")
                    else:

                       # if(re.search(r'^\s*\&sect\;\s*([\d]+)\.?\s*$', p.getText())):
                        #    addtional_section_anchor="<a name='section_"+re.search(r'^\s*\&sect\;([\d]+)\.?\s*$', p.getText()).group(1)+">section</a>"

                        #p.replaceWith("<p>"+combined+"</p><div id='paragraph_number_"+m.group(1)+"_"+language+"'>"+p.getText()+"</div>")
                        #combined=re.sub(r'\&sect\;\s*([\d]+)\.?\s*',r'<span name="section_\g<1>"></span>&sect; \g<1>. ',combined)
                        combined=re.sub(r'(Pagina|Page)\-([\d]+)',r'<span class="page_number" name="page_\g<2>">Page \g<2></span> ',combined)
                        #req.write("%s<br/><br>"%m.group(1))
#                         if(m.group(1)=='0'):
#                             req.write('Processing special paragraph<br/><br/>')
#                             s=''
#                             for c in p.contents:
#                                 s += unicode(c)
#                             p.replaceWith("asdf"+combined+"<div id='paragraph_number_"+m.group(1)+"_"+language+"'>"+s+"</div>")
#                         else:
#                         if(number!=max):
                        p.replaceWith("<div class='translation'><p>"+combined+"</p></div><div id='paragraph_number_"+str(number)+"_"+language+"'>"+p.getText()+"</div>")
                    combined=''
                else:
                    for i in p.contents:
                        #req.write('name=%s<br/>'%i)
                        for section in soup.findAll('p',text=re.compile(r'^&sect\;\s*([\d]+\w?)\.?\s*')):
                            text=re.compile(r'^&sect\;\s*([\d]+\w?)\.?\s*')
                            m=text.match(section)
                            if(m.group(1)=='0'):
                                #req.write("Special section %s"%m.group(1))
                                section='<td width="50%%">%s</td>'%(re.sub(r'&sect\;\s*([\d]+\w?)\.?\s*',r'',section))
                            else:
                                section='<td width="50%%"><a class="section_link" href="#section_%s">&sect; %s</a>. %s</td>'%(m.group(1),m.group(1),re.sub(r'&sect\;\s*([\d]+\w?)\.?\s*',r'',section))
                            sections.append(section)
                            #req.write('Found section: %s<br/><br/>'%(section))
                            
                        i=re.sub(r'\s*\&sect\;\s*(0+\w?)\.?\s*',r'',unicode(i))
                        i=re.sub('-X-','&nbsp;&nbsp;&nbsp;',i)
                        i=re.sub('-#--#-','<span class="newline"></span>',i)
                        i=re.sub('-#-','<br/>',i)
                        i=re.sub('<p></p>','',i)
                        combined+=re.sub(r'^\s*(<[^>]+>)\s*\&sect\;\s*([^0][\d]*\w?)\.?\s*',r'\g<1><span name="section_\g<2>" id="section_\g<2>"></span>&sect; \g<2>. ',unicode(i))

                    p.replaceWith('')

            except Exception,e:
                req.write('Something went wrong processing the text, %s<br/>'%e.message)
                raise

        k=0
        soup=BeautifulSoup("%s"%soup)
        #req.write('combined=%s'%combined)
        #req.write("\n\nsoup_before=%s<br/>\n"%soup)
        for d in soup.findAll("div"):
            k+=1

            #req.write('k=%s'%k)
            #req.write('len=%s'%len(soup.findAll('div')))
            if(k==len(soup.findAll('div'))):
                if(language==left):
                    d.replaceWith("<div id='paragraph_number_"+str(number)+"_"+language+"'>"+str(number)+".</div><div class='original_text'>"+combined+"</div>")
                else:
                    d.replaceWith("<div id='paragraph_number_"+str(number)+"_"+language+"'>"+str(number)+".</div><div class='translation'><p>"+combined+"</p></div>")
        soup=BeautifulSoup("%s"%soup)
        #req.write("\n\nsoup_after=%s<br/>\n"%soup)
        return (sections,soup,max)

def tocTranslation(language):
    if(language=="french"):
        return u"Table des matières"
    if(language=="english"):
        return u'Table of contents'
    if(language=="dutch"):
        return u'Inhoud'
    if(language=="test"):
        return u'Test'

def createTOC(sections_original,org_language,sections_translation,translation_language,req):
    toc='<table id="toc_translation" width="100%" cellspacing="0" cellpadding="4"><colgroup> <col width="128*" /> <col width="128*" /> </colgroup><tbody>'
    toc+='<tr valign="TOP">'
    toc+='<td><div id="page-title">'+tocTranslation(org_language)+'</div></td>'
    toc+='<td><div id="page-title">'+tocTranslation(translation_language)+'</div></td>'
    toc+='</tr>'
    for i in range(min(len(sections_original),len(sections_translation))):

        toc+='<tr valign="TOP">'+sections_original[i]+sections_translation[i]+'</tr>'
    toc+='</tbody></table><h1></h1>'
    return toc


def createSideBySide(soup_org,language_org,soup_translation,language_translation,max_org,max_translation,req):


    side_by_side='<table width="100%" cellspacing="0" cellpadding="4"><colgroup> <col width="128*" /> <col width="128*" /> </colgroup><tbody>'
    #req.write("\n\nsoup sbs=%s<br/>\n\n"%max_org)
    #req.write("\n\nsoup sbs=%s<br/>\n\n"%max_translation)

    if(max_org>max_translation):
        max=max_org
    else:
        max=max_translation
    #req.write("\n\nsoup sbs=%s<br/>\n\n"%max)
    for i in range(1,int(max)+1):

        #req.write('i=%s<br/>'%i)
        paragraph_org=''
        paragraph_translation=''
        try:

            #req.write('finding %s %s<br/>'%(str(i),language_org))
            org_number= soup_org.find("div",id="paragraph_number_"+str(i)+"_"+language_org)
            #id_div="paragraph_number_"+str(i)+"_"+language_org
            #org_number= soup_org.find("div",{id:"paragraph_number_0_dutch"})
            #req.write('incoming soup_org=%s'%soup_org)
            #req.write('org_number: %s'%org_number)
            paragraph_org=org_number.nextSibling

            #if(i==530): 
                #req.write("%s next sibling:%s<br/>"%(i,paragraph_org.getText()))
                #req.write(re.match(r'\d+\.?',paragraph_org.getText()))
                
            # failsafe if a paragraph number is the next sibling
            if(re.match(r'^\d+\.?$',paragraph_org.getText())):
                #req.write('matched org!')
                paragraph_org=paragraph_org.nextSibling
                #req.write("next sibling:%s<br/>"%paragraph_org)
            #req.write("tag org=%s<br/>"%paragraph_org)
        except AttributeError,e:

            req.write('<div class="error">No paragraph %s found for %s </div><br/>'%(i,language_org))

            #raise
        try:
            #req.write('finding %s %s<br/>'%(str(i),language_translation))
            #id_div="paragraph_number_"+str(i)+"_"+language_translation
            translation_number=soup_translation.find("div",id="paragraph_number_"+str(i)+"_"+language_translation)
            #translation_number= soup_translation.find("div",{id:id_div})
            tn=translation_number.nextSibling
            
            # failsafe if a paragraph number is the next sibling
            if(re.match(r'^\d+\.?$',tn.getText())):
                #req.write('matched tr!')
                tn=translation_number.nextSibling

            #req.write("next sibling:%s<br/>"%tn.getText())
            if(tn==None):
                paragraph_translation=''
            else:
                paragraph_translation=tn
            #req.write("tag translation=%s<br/>"%paragraph_translation)
        except AttributeError,e:
            req.write('<div class="error">No paragraph %s found for %s </div><br/>'%(i,language_translation))
            #req.write('soup_translation='%soup_translation)
            #raise
        #req.write("tag org=%s<br/>"%paragraph_org)
        #req.write("tag tranlation=%s<br/>"%paragraph_translation)
        side_by_side+='<tr valign="TOP"><td width="50%%"><a class="ref_nr" name="paragraph_%s" href="#paragraph_%s">%s</a>%s</td><td width="50%%"><a class="ref_nr" href="#paragraph_%s">%s</a>%s</td></tr>'%(i,i,i,paragraph_org,i,i,paragraph_translation)
    side_by_side+='</tbody></table>'
    return side_by_side

def getCSS(soup,replacement,req):
    try:
        css= soup.find("style")
        css_string=re.sub(r'}',"}\n"+replacement,css.string)
        css_string=re.sub(r''+replacement+'$','',css_string)
        #req.write("AFTER NEWLINES::<br/><br/>")
        #req.write(css_string);
        #css_string=re.sub(r'font-weight:bold;','font-weight:bold;display: block;padding-bottom:15px;margin-top:15px;',css_string)
        css_string=re.sub(r'font-weight:bold(?!;)','font-weight:bold;display: block;padding-bottom:15px;margin-top:15px;',css_string)
        css_string=re.sub(r'font-size:12pt(?!;)','',css_string)
        css_string=re.sub(r'font-family:"[^"]+"(?!;)','',css_string)
        #req.write("AFTER FONT WEIGHT BOLD::<br/><br/>")
        #req.write(css_string);

        subst='<style type="text/css" media="all">#page_wrapper{font-size:10pt}'+css_string+"</style>"
    except Exception,e:
        #req.write('<div class="error">%s</div>'%e.message)
        raise
    return subst



def createStandalone(soup_org,language_org,max_org,req):


    standalone=''
    #req.write("\n\nsoup sbs=%s<br/>\n\n"%max_org)
    #req.write("\n\nsoup sbs=%s<br/>\n\n"%max_translation)

    #req.write("\n\nsoup sbs=%s<br/>\n\n"%max)
    for i in range(1,int(max_org)+1):

        #req.write('i=%s<br/>'%i)
        paragraph_org=''
        paragraph_translation=''
        try:

            #req.write('finding %s %s<br/>'%(str(i),language_org))
            org_number= soup_org.find("div",id="paragraph_number_"+str(i)+"_"+language_org)
            #id_div="paragraph_number_"+str(i)+"_"+language_org
            #org_number= soup_org.find("div",{id:"paragraph_number_0_dutch"})
            #req.write('incoming soup_org=%s'%soup_org)
            #req.write('org_number: %s'%org_number)
            paragraph_org=org_number.nextSibling

            #if(i==530): 
                #req.write("%s next sibling:%s<br/>"%(i,paragraph_org.getText()))
                #req.write(re.match(r'\d+\.?',paragraph_org.getText()))
                
            # failsafe if a paragraph number is the next sibling
            if(re.match(r'^\d+\.?$',paragraph_org.getText())):
                #req.write('matched org!')
                paragraph_org=paragraph_org.nextSibling
                #req.write("next sibling:%s<br/>"%paragraph_org)
            #req.write("tag org=%s<br/>"%paragraph_org)
        except AttributeError,e:

            req.write('<div class="error">No paragraph %s found for %s </div><br/>'%(i,language_org))

            #raise
            #req.write('soup_translation='%soup_translation)
            #raise
        #req.write("tag org=%s<br/>"%paragraph_org)
        #req.write("tag tranlation=%s<br/>"%paragraph_translation)
        #if(paragraph_org is not None):
            #paragraph_org=re.sub('-X-','&nbsp;&nbsp;&nbsp;','%s'%paragraph_org)    
            #regex=re.compile(r'{([^}]+)}')
            #paragraph_org=re.sub(r'\{([^})\}','\1',paragraph_org) 
        if(i==0):
            i_show=''
        else:
            i_show=i
        standalone+='<span class="ref_nr_standalone"><a name="paragraph_%s" href="#paragraph_%s">%s</a></span>%s'%(i,i,i_show,paragraph_org)
    return standalone





def handler(req,merchant=None):
    try:
        form = util.FieldStorage(req,keep_blank_values=1)
        org_language=form.get("org",None)
        translation_language=form.get('translation',None)
        req.log_error('handler')
        req.content_type = 'text/html'
        if(form.get("org",None)==None or form.get("translation",None)==None):
            raise Exception ('no org and translation parameter, use /translationHandler.py?org=dutch&translation=english for example')


        req.write('<html><head>  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>Downloading feeds</title><link rel="stylesheet" href="script.css" type="text/css"></head><body>')
        req.write("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>")
        req.flush()
        #req.write(os.path.dirname(os.path.realpath(__file__)))

        #connect to Google drive
        client = gdata.docs.service.DocsService('dpspproject')
        req.write('Connecting to Google Drive...<br/>');
        client.ClientLogin(gdusername, gdpasswd)

        response = GetResourcesWithExponentialBackoff(req,org_language,translation_language,client)




        f_original = open(os.path.dirname(os.path.realpath(__file__))+'/'+org_language+'.html', 'rw')
        f_translation = open(os.path.dirname(os.path.realpath(__file__))+'/'+translation_language+'.html', 'rw')
        html_original=f_original.read()
        html_translation=f_translation.read()
        soup_original=BeautifulSoup(html_original)
        soup_translation=BeautifulSoup(html_translation)
        #req.write('html_dutch = %s' % soup_original)
        #soup_english.prettify()
        (sections_translation,soup_translation,max_translation)=cleanupHTML(soup_translation,translation_language,org_language,req)
        #req.write('html_english = %s' % soup_english1)
        #req.write('html_dutch = %s' % soup_original)
        (sections_original,soup_original,max_org)=cleanupHTML(soup_original,org_language,org_language,req)
        #req.write('html_dutch = %s' % soup_original)
        soup_original=BeautifulSoup("%s"%soup_original)

        #soup_translation=BeautifulSoup("%s"%soup_translation)
        #for section in sections_original:
            #req.write("Section : %s<br/><br/>"%section)
        #for section in sections_translation:
            #req.write("Section : %s<br/><br/>"%section)
        soup_original.prettify()
        soup_translation.prettify()
        soup_original=BeautifulSoup("%s"%soup_original)
        soup_translation=BeautifulSoup("%s"%soup_translation)
        #req.write('html_english = %s' % soup_english)
        #req.write('html_dutch = %s' % soup_original)
        #req.write('Creating side by side...<br/>')
        side_by_side=getCSS(soup_original,'.original_text span',req)

        side_by_side+=getCSS(soup_translation,'.translation span',req)
        org_number= soup_original.find("div",id="paragraph_number_"+str(0)+"_"+org_language)
        paragraph_org=org_number.nextSibling
        translation_number=soup_translation.find("div",id="paragraph_number_"+str(0)+"_"+translation_language)
        tn=translation_number.nextSibling
        s=''
        for p in tn.findAll('p'):
            for c in p.contents:
                s+=unicode(c)
        tn.replaceWith(s)

        side_by_side+='<table width="100%" cellspacing="0" cellpadding="4"><colgroup> <col width="128*" /> <col width="128*" /> </colgroup><tbody>'
        side_by_side+='<tr valign="TOP"><td width="50%%"><div class="original_text">%s</div></td><td width="50%%"><div class="translation">%s</div></td>'%(paragraph_org,s)
        side_by_side+='</tbody></table>'
        #side_by_side+=createTOC(sections_original,org_language,sections_translation,translation_language,req)
        #req.write('html_dutch = %s' % soup_original)
        
        side_by_side+=createSideBySide(soup_original,org_language,soup_translation,translation_language,max_org,max_translation,req)

        db = MySQLdb.connect(host="localhost",user=dbuser,passwd=dbpasswd,db=dbdb,charset = "utf8", use_unicode = True)
        cursor = db.cursor()
        sql = """update """+dbtable+""" set post_content=%s where post_name='"""+org_language+"""-"""+translation_language+"""'"""
        #sql = "update "+dbtable+" set post_content="+side_by_side+" where id=80"
        #req.write("%s <br/>"% sql)
        req.write('Updating post in Wordpress...<br/>')
        try:
            # Execute the SQL command
            cursor.execute(sql,side_by_side)
        except Exception,e:
            req.write(e.message)
        try:
            req.write("%s<br/><br/>"%os.path.dirname(os.path.realpath(__file__)))
            os.remove(os.path.dirname(os.path.realpath(__file__))+'/../../cake/app/tmp/cache/cake_'+org_language+'-'+translation_language)
            req.write('removed cache file<br/><br/>')
        except Exception,e:
            req.write ('Could not remove cache file, not present<br/>\n')
            pass

        #standalone article
        
        update_standalone=form.get("update_standalone",None)
        if(update_standalone!=None):
            if(org_language==update_standalone):
                try:
                    req.write('updating stand alone article from original %s<br/><br/>'%update_standalone)
                    standalone=getCSS(soup_original,'.original_text span',req)
                    standalone+=createStandalone(soup_original,org_language,max_org,req)
                    # Execute the SQL command
                    sql_standalone = """update """+dbtable_research+""" set post_content=%s where post_name='"""+org_language+"""'"""
                    cursor.execute(sql_standalone,standalone)
                    # Commit your changes in the database
                    db.commit()     
    
                except Exception as e:
                    #req.write(e)     
                    raise       
            else:
                if(translation_language==update_standalone):
                    try:
                        req.write('updating stand alone article from translation %s<br/><br/>'%update_standalone)
                        standalone=getCSS(soup_translation,'.translation span',req)
                        standalone+=createStandalone(soup_translation,translation_language,max_translation,req)
                        # Execute the SQL command
                        sql_standalone = """update """+dbtable_research+""" set post_content=%s where post_name='"""+translation_language+"""'"""
                        cursor.execute(sql_standalone,standalone)
                        # Commit your changes in the database
                        db.commit()     
                    except Exception,e:
                        req.write(e)
                        raise   
            req.write('sql = %s<br/>'%sql_standalone)
            req.write('sbs = %s<br/>'%standalone)
        # disconnect from server
        db.close()        
            #req.write('type: %s<br/>'%str(type(paragraph)))
            #req.write("next tag=%s<br/>"%paragraph.nextSibling)
#         found=False
#         while(found==False):
#             paragraph=BeautifulSoup(paragraph.next)
#             req.write("p=%s<br/>"%paragraph)
#             if(paragraph.find("div",id="paragraph_number_1_dutch")):
#                 found=True
#             if(paragraph.next)
        #req.write('p=%s<br/>'%p.nextSibling)
            #req.write('p.next=%s<br/>',p.next_sibling)


#         req.write("hex1: %s<br/>"%hex(ord(u'§')))
#         req.write("hex2: %s<br/>"%hex(ord(u'§')))
#         req.write("u:%s<br/>" % u'\u00A7'.encode('UTF-8'))



#         for p in soup.findAll('p'):
#             string=""
#             for i in p.contents:
#                 string+=unicode(i)
#             #req.write("contents=%s</br>"%p.contents)
#             #p.replaceWith("<div id='content'>"+string+"</div>")
#             p.replaceWith(""+string+"")
        #for nr in soup.find(text=re.compile(r'^\s*\d+\s*\.\s*$')):
        #for nr in soup.findAll(text=re.compile(r'^\s*\d+\s*\.\s*$',re.MULTILINE)):
#         for nr in soup.findAll('div',text=re.compile('^\s*\d+\s*\.\s*',re.MULTILINE)):
#             req.write('match=%s<br/>\n'%nr)

        req.write('side_by_side = %s' % side_by_side.encode('utf-8'))
        #req.write('html_dutch = %s' % soup_dutch)
        #req.write('html_english = %s' % html_english)
        req.write('Done<br/>')
        req.write('</body></html>')
    except Exception,e:
        req.write("Exception main handler: %s" %e.message)
        raise
    return apache.OK

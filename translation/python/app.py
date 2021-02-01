import os
from flask import Flask, flash, request, redirect, url_for
from werkzeug.utils import secure_filename
import translationHandlerFlask
import socket

if socket.gethostname() !='':
    import MySQLdb
import ConfigParser
from flask_basicauth import BasicAuth

parser = ConfigParser.SafeConfigParser()

parser.read(os.path.dirname(os.path.abspath(__file__)) + '/' + socket.gethostname() + '.ini')
if socket.gethostname() !='':

    dbuser = parser.get('db', 'user')
    # dbuser="hgkwmerchants"
    dbpasswd = parser.get('db', 'passwd')
    dbdb = parser.get('db', 'db')
    dbtable = parser.get('db', 'dbtable')
    dbtable_research = parser.get('db', 'dbtable_research')

    gdusername = parser.get('gd', 'username')
    gdpasswd = parser.get('gd', 'passwd')


UPLOAD_FOLDER = '/tmp'
ALLOWED_EXTENSIONS = set(['html'])

app = Flask(__name__)
app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER
app.secret_key = "super secret key"
app.config['BASIC_AUTH_FORCE'] = True
basic_auth = BasicAuth(app)


if socket.gethostname() !='':


    def write_mysql_standalone(slug, standalone):
        db = MySQLdb.connect(host="localhost", user=dbuser, passwd=dbpasswd, db=dbdb, charset="utf8", use_unicode=True)
        cursor = db.cursor()
        # Execute the SQL command
        sql_standalone = """update """ + dbtable_research + """ set post_content=%s where post_name='""" + slug + """'"""
        print(sql_standalone)
        # print(standalone)
        standalone=standalone.replace("\n",' ')
        standalone=standalone.replace("\r",' ')
        standalone=standalone.replace("expected string or buffer",'')
        try:
            # Execute the SQL command
            cursor.execute(sql_standalone, (standalone,))
            db.commit()
            print('exectured')
        except Exception as e:
            print("ERROR")
            print(e)


    def write_mysql(slug, side_by_side, standalone=None):
        db = MySQLdb.connect(host="localhost", user=dbuser, passwd=dbpasswd, db=dbdb, charset="utf8", use_unicode=True)
        cursor = db.cursor()
        side_by_side=side_by_side.replace("\n",' ')
        side_by_side=side_by_side.replace("\r",' ')
        side_by_side='[no_toc]'+side_by_side
        sql = """update """ + dbtable + """ set post_content=%s where post_name='""" + slug + """'"""
        print('Updating post in Wordpress...<br/>')
        print('sql statement = {}'.format(sql))

        try:
            # Execute the SQL command
            cursor.execute(sql, (side_by_side,))
            db.commit()
        except Exception as e:
            print("ERROR")
            print(e)

def allowed_file(filename):
    return '.' in filename and \
           filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS

@app.route('/', methods=['GET', 'POST'])
@basic_auth.required
def upload_file():
    # check if the post request has the file part
    if 'file' in request.files:
        file = request.files['file']
        # if user does not select file, browser also
        # submit an empty part without filename
        if file.filename == '':
            flash('No selected file')
            return redirect(request.url)
        if file and allowed_file(file.filename) and 'left' not in request.values:
            filename = secure_filename(file.filename)
            file.save(os.path.join(app.config['UPLOAD_FOLDER'], filename))
            return redirect(url_for('upload_file',
                                    left=filename))
        elif file and allowed_file(file.filename) and 'right' not in request.values:
            filename = secure_filename(file.filename)
            file.save(os.path.join(app.config['UPLOAD_FOLDER'], filename))
            return redirect(url_for('upload_file',left=request.values['left'],
                                    right=filename))
        return result_html
    elif 'slug' in request.values:
        if 'skip' in request.values and request.values['skip']!='':
            print('skip = {}'.format(request.values['skip']))
            if 'upload_standalone' in request.values and request.values['upload_standalone']!='none':
                
                upload_standalone=request.values[request.values['upload_standalone']]
                result, result_html, standalone = translationHandlerFlask.go(request.values['left'],request.values['right'],request.values['slug'],upload_standalone,request.values['skip'])
            else:
                result, result_html, standalone = translationHandlerFlask.go(request.values['left'],request.values['right'],request.values['slug'],None,request.values['skip'])

        else:
            if 'upload_standalone' in request.values  and request.values['upload_standalone']!='none':
                upload_standalone=request.values[request.values['upload_standalone']]
                result, result_html, standalone = translationHandlerFlask.go(request.values['left'],request.values['right'],request.values['slug'], upload_standalone)
            else:
                result, result_html, standalone = translationHandlerFlask.go(request.values['left'],request.values['right'],request.values['slug'])
            
        if socket.gethostname() !='Jelmers-MacBook-Pro-2.local':
            if 'upload_standalone' in request.values  and request.values['upload_standalone']!='none':
                write_mysql_standalone(request.values['slug'], standalone)
            else:
                write_mysql(request.values['slug'], result)
                
        return result_html
        
    if 'left' not in request.values:
        return '''
        <!doctype html>
        <title>Upload left File</title>
        <h1>Upload left File</h1>
        <form method=post enctype=multipart/form-data>
        <input type=file name=file>
        <input type=submit value=Upload>
        </form>
        '''
    elif 'right' not in request.values:
        return '''
        <!doctype html>
        <title>Upload right File</title>
        <h1>Upload right File</h1>
        <form method=post enctype=multipart/form-data>
        <input type=file name=file>
        <input type=submit value=Upload>
        </form>
        '''
    elif 'slug' not in request.values:
        return '''
        <!doctype html>
        <title>Enter slug</title>
        <h1>Enter slug</h1>
      <form action="" method="post">
        <input type="text" name="slug"/>

                <div style='margin-top:15px;'>
                <h2>Upload standalone?</h2>
  <input type="radio" id="left" name="upload_standalone" value="left">
  <label for="left">Left</label>
</div>

<div>
  <input type="radio" id="right" name="upload_standalone" value="right">
  <label for="right">Right</label>
</div>

<div style='margin-bottom:15px;'>
  <input type="radio" id="none" name="upload_standalone" value="none" checked>
  <label for="none">None</label>
</div>
<div style='margin-bottom:15px;'>
        <h2>Skip?</h2>
        <input type="text" name="skip"/>
</div>
        <input type="submit" name="SubmitButton"/>

        </form>
        '''
   

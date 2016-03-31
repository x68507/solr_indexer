solr_indexer
============

Full-text SOLR instance with user friendly UI for searching PDF and other documents.

This project was origianlly developed for two reason:

 1. Enable an in-house full text search engine for indexing 2,500+ pages of user manuals
 2. Allow controlled sub-directory searches

The complete web app mimics Google Drive, but adds the additional functionality that you can choose which folders/subfolders the SOLR server searches through, which is an invaluable characteristic when it comes to different functional groups trying to find information in a folder directory.  The following are key characteristics of the web app

 - Apache SOLR Indexer for full-text searching
 - Apache Tika for text extraction and document parsing; accepts a wide variaty of formats, including PDF and most MS Office documents
 - Built-in PDF viewer based on Mozilla's PDF.js project; allows for emailing direct links to a PDF document without having to download a file first
 - Responsive design to allow for both desktop & mobile searching and viewing

#Key Requirement
 - Written for execution on a Windows VM; tested on both Windows 7 and Windows Server 2012
 - Java Runtime Environment (JRE); the Java Software Development Kit (JDK) will also work, but is not required
  - Make sure that JAVA_HOME is defined as an environment variable
 - Apache Tika JAR executible (written for v1.8)
 - Apache SOLR Server (written for v4.10)
 - PHP (used as a bundled package with XAMPP; >v5.4)
  - Make sure that PHP.exe is set in the PATH environment variable
 - Composer (required for building Funstaff's PHP Tika's wrapper)

#Command Line
For some PHP installs, a user does not have sufficient privileges to execute commands in PHP (i.e., default settings for AWS Windows Server 2012 and the out-of-the-box install of XAMPP).  To ensure that SOLR server and Tika parser works, you can always use CLI rather than the *Panel* web interface.
##Starting Server
`C:\tika\solr\bin\solr.cmd start -h localhost`
##Updating Files
`c:\xampp\php\php.exe c:\xampp\htdocs\solr\panel\tika.php c:\xampp\htdocs\solr\docs`
##Stopping Server
`C:\tika\solr\bin\solr.cmd stop -p 8983`
##Deleting Index
When updating multiple files, you want to make sure to delete the "old" versions of the file.  In this basic example, it is easier to just delete the entire previous index and re-index the entire directory.  For a *t2.micro* AWS instance, it takes about 5 minutes to index 1,500 pages.

To delete files in the index, you can simply use the cURL feature of the SOLR server
`http://localhost:8983/solr/update?stream.body=%3Cdelete%3E%3Cquery%3E*:*%3C/query%3E%3C/delete%3E`

BiberOnMoodle
=============

Informatik-Biber (Bebras Competition)
-------------------------------------
The Informatik-Biber (Bebras Competition) is Germany's biggest competition for school students of all ages in the field of informatics (computer science). Students from grades 3 to 13 can participate. The Informatik-Biber is the German partner of the international "Bebras Challenge", initiated in 2004 in Lithuania. In Germany, the Informatik-Biber has been promoting digital thinking with tasks that relate to everyday life since 2007. Participants discover fascination and relevance of computer science. With the Informatik-Biber, even very young students can explore the versatility of computer science playfully and naturally.

Further information on the competition can be found at the [official website](https://bwinf.de/biber/).

Tasks for Moodle from the Informatik-Biber Archive
--------------------------------------------------
The tasks from the archive of the German Informatik-Biber can now be used at schools to deepen their understanding of computer science. They are readily available, in German, as a [question bank](https://docs.moodle.org/401/en/Question_bank) for Moodle. After import, they can be used in tests and courses. We currently provide almost all of the tasks from the years 2014 to 2021. In some cases, they contain explanations of the solutions that the students can see after they have worked on the tasks.

Details and Technical Guidelines
--------------------------------
Given that many of the tasks are interactive, it is required to install this question type plugin. Afterwards, the tasks can be imported as a question bank, see below.

Tags that estimate the tasks' difficulties for different grades are attached to the questions in Moodle. The questions can be filtered for these tags.

Moodle Plugin
-------------
To install the plugin, extract the ZIP archive and copy the folder to `[...]/moodle/question/type/`, where `[...]/moodle/` is the path to your Moodle installation. The questions can now be imported.

Further information on how to install a plugin can be found [here](https://docs.moodle.org/401/en/Installing_plugins).

Importing the Tasks
-------------------
The tasks are available as XML files (one for each competition year). We advise to create a "dummy course" where you will import the tasks, so that they can always be accessed by teachers by going there. They can still be used in every course. From the course page, select `Question bank > Import`, then choose `Moodle XML format` and `Get category from file`. Upload the XML file and start the import.

Question Banks Available
------------------------
The following years are currently available. (The links will be added shortly.)

 * biber2014.xml
 * biber2015.xml
 * biber2016.xml
 * biber2017.xml
 * biber2018.xml
 * biber2019.xml
 * biber2020.xml
 * biber2021.xml

Troubleshooting
---------------
 - *Uploading the file seems to take very long or is rejected.* File size is restricted by Moodle, the PHP configuration, as well as the HTTP webserver (Apache / NGINX / ...). The task collections have a size of up to 35 MB. Information on these restrictions and how to raise them can be found [here](https://docs.moodle.org/311/en/File_upload_size).

----

For feedback, please write to `biber` AT `bwinf.de`.

Copyright BWINF - GI e.V. Lizenz: CC BY-SA 4.0

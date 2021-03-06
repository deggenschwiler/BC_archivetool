from __future__ import print_function
from datetime import date, datetime, timedelta
import os
import glob
import mysql.connector
from mysql.connector import errorcode

''' This will make two tables for every connected drive mounted at Volumes with
BC as it's start. It will delete exsisting tables and replace them with up to
date file and foldr lists respectively  '''


''' Get the name of the drive to scan '''
''' Scan the list of files in a particular HDD, save each filename as an item in a list of Strings. '''
didit = 0
for dir in os.listdir('/Volumes'):
    if(didit > 0): again = "next "
    else: again = "first "
    print("\nScanning " + again + "drive: " + dir)
    if (os.path.ismount(os.path.join('/Volumes', dir)) and dir[0] == "B" and dir[1] =="C"):
        didit = 1
        ''' Connect to mysql database on BC '''
        try:
          cnx = mysql.connector.connect(user='brandcal_archive', password='Ut@#,5yP(?GM3P',
                                      host='91.208.99.2', port='1188',
                                      database='brandcal_archive')
        except mysql.connector.Error as err:
          if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
            print("\n  Something is wrong with your user name or password")
          elif err.errno == errorcode.ER_BAD_DB_ERROR:
            print("\n  Database does not exist")
          else:
            print(err)
        else:
            print('  Connected to mySQL database at Brand Calibre')
        #this is an empty list for filnames to be added
        scanf = []
        scanfastf = []
        fastf = []
        f = []
        drive = dir
        for filename in glob.iglob('/Volumes/' + dir + '/**', recursive=True):
            scanf.append(filename) #add filename to list scanf
            if(os.path.isdir(filename)): scanfastf.append(filename)

        ''' If the table doesn't exist for that drive, create it, if it does... delete it '''
        checktable = cnx.cursor()

        checktable.execute("DROP TABLE IF EXISTS brandcal_archive." + drive )
        checktable.execute("CREATE TABLE brandcal_archive." + drive + "( `Filename` VARCHAR(255) NOT NULL ) ENGINE = InnoDB;")
        checktable.close()

        checkfast = cnx.cursor()

        checkfast.execute("DROP TABLE IF EXISTS brandcal_archive.FAST_" + drive )
        checkfast.execute("CREATE TABLE brandcal_archive.FAST_" + drive + "( `Filename` VARCHAR(255) NOT NULL ) ENGINE = InnoDB;")
        checkfast.close()

        ''' Prune that list of everything which isn't a directory and save it as a FAST list '''

        for file in scanf:
            line = file[9:].replace('"', '')
            f.append(line)
        for file in scanfastf:
            line = file[9:].replace('"', '')
            fastf.append(line)

        ''' Change that list into mySQL table format '''

        cursor1 = cnx.cursor()
        add_file1 = (
                """INSERT INTO brandcal_archive.""" + drive +
                """(`Filename`) VALUES """)
        valuestring1 = ""
        count1 = 0
        for file1 in f:
            valuestring1 += "(\"" + file1 + "\")"
            if (count1 + 1 == len(f)): valuestring1 += ";"
            else: valuestring1 += ", "
            count1 += 1
        add_file1 += valuestring1

        ''' And the ffast list into mySQL table format '''

        cursor2 = cnx.cursor()
        add_file2 = (
                "INSERT INTO brandcal_archive.FAST_" + drive +
                "(`Filename`) VALUES ")
        valuestring2 = ""
        count2 = 0
        for file2 in fastf:
            valuestring2 += "(\"" + file2 + "\")"
            if (count2 + 1 == len(fastf)): valuestring2 += ";"
            else: valuestring2 += ", "
            count2 += 1
        add_file2 += valuestring2

        ''' Run a mySQL command to update the table for that drive as defined by the name of the drive.'''
        cursor1.execute(add_file1)
        cursor2.execute(add_file2)
        cnx.commit()
        # confirm it worked to console
        print( "  " + drive + " search index successfully updated.\n")
        cnx.close()
    else: print("\nPlease rename the drive you want to add to the search tool in the format:\n\n    \"BC_Archive_XX\"\n\nwhere XX is a number between 00 and 99")
print("\n  All done here, connection closed.\n")
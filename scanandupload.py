''' Connect to mysql database on BC '''
from __future__ import print_function
from datetime import date, datetime, timedelta
import os
import glob
import mysql.connector
from mysql.connector import errorcode

try:
  cnx = mysql.connector.connect(user='brandcal_archive', password='Ut@#,5yP(?GM3P',
                              host='91.208.99.2', port='1188',
                              database='brandcal_archive')
except mysql.connector.Error as err:
  if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
    print("Something is wrong with your user name or password")
  elif err.errno == errorcode.ER_BAD_DB_ERROR:
    print("Database does not exist")
  else:
    print(err)
else:
    print('connected successfully to mySQL db at brandcalibre')

#this is an empty list for filnames to be added
scanf = []
fastf = []
f = []

''' Get the name of the drive to scan '''
''' Scan the list of files in a particular HDD, save each filename as an item in a list of Strings. '''

for dir in os.listdir('/Volumes'):
    if (os.path.ismount(os.path.join('/Volumes', dir)) and dir[0] == "B" and dir[1] =="C"):
        drive = dir
        for filename in glob.iglob('/Volumes/' + dir + '/**', recursive=True):
            scanf.append(filename) #add filename to list scanf

''' If the table doesn't exsist for that drive, create it, if it does... delete it '''
#tablemaker = cnx.cursor()
#datatomake = (
#        "CREATE TABLE brandcal_archive." + drive +
#        " ( `Filename` VARCHAR(255) NOT NULL ) ENGINE = InnoDB;")



''' Prune that list of everything which isn't a directory and save it as a FAST list '''

for file in scanf: f.append(file[9:])
for file in f:
    if file.endswith("/"): fastf.append(file)

''' Change that list into mySQL table format '''

cursor = cnx.cursor()
add_file = (
        "INSERT INTO brandcal_archive." + drive +
        "(`Filename`) VALUES ")
valuestring = ""
count = 0
for file in f:
    valuestring += "('" + file + "')"
    if (count + 1 == len(f)): valuestring += ";"
    else: valuestring += ", "
    count += 1
add_file += valuestring

''' Run a mySQL command to update the table for that drive as defined by the name of the drive.'''
cursor.execute(add_file)
cnx.commit()

# confirm it worked to console
print("Archive search updated.")
cnx.close()

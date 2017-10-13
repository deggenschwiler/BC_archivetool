# find BC\ Archive\ 0006/ -type d > ~/FAST_BC6.txt


# Connect to mysql database on BC
import mysql.connector
from mysql.connector import errorcode

try:
  cnx = mysql.connector.connect(user='brandcal',
                                database='testt')
except mysql.connector.Error as err:
  if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
    print("Something is wrong with your user name or password")
  elif err.errno == errorcode.ER_BAD_DB_ERROR:
    print("Database does not exist")
  else:
    print(err)
else:
  cnx.close()

# Set the name of the drive to scan

# Scan the list of files in a particular HDD, save each filename as an item in a list of Strings.

# Prune that list of everything which isn't a directory and save it as a FAST list

# Change that list into mySQL table format, or maybe csv?

# run a mySQL command to update the table for that drive as defined by the name of the drive.

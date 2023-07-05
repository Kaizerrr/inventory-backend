# import csv


# with open('INACTIVE-ACTIVE-JACKET-MONITORING.csv', 'r') as file:
#     reader = csv.reader(file, delimiter='\t')
#     next(reader) # skip first header row
#     next(reader) # skip second header row
#     for row in reader:
#         print(row)



# import csv
# import mysql.connector

# # Open CSV file and read data
# with open('INACTIVE-ACTIVE-JACKET-MONITORING.csv', 'r') as file:
#     reader = csv.reader(file)
#     data = [tuple(row) for row in reader]

# # Connect to MySQL database
# cnx = mysql.connector.connect(user='root', password='',
#                               host='localhost', port=3306, database='user', )

# # This is the code for checking if the connection is established
# if cnx.is_connected():
#     print('Connected to MySQL database')


# cursor = cnx.cursor()

# # Construct query to insert data into MySQL table
# table_name = 'card_activation'
# cols = '(fileNumber, studentId, name, program, storageLocation, studentClass)' # replace with your column names
# query = f"INSERT INTO {table_name} {cols} VALUES (%s, %s, %s, %s, %s, %s)" # replace with appropriate placeholders


# # Execute query to insert data into MySQL table
# cursor.executemany(query, data)
# cnx.commit()

# # Close database connection
# cursor.close()
# cnx.close()



# ----------------------------------------------------------------------------------------------------------------------

import csv
import mysql.connector

# Establish database connection
cnx = mysql.connector.connect(user='root', password='',
                              host='localhost', port=3306, database='laravel')
cursor = cnx.cursor()
ACTIVE_STATUS = 0

# Prepare MySQL queries
card_activation_query = "INSERT INTO students (fileNumber, studentId, activeStatus, name, program, studentClass, storageLocation) VALUES (%s, %s, %s, %s, %s, %s, %s)"
user_requirements_query = "INSERT INTO student_requirements (student_id, requirement_id, submitted, note) VALUES (%s, %s, %s, %s)"

# Load data from CSV file
with open('IA-A-B.csv', 'r') as file:
    reader = csv.reader(file)
    row_count = 0  # initialize row count 
    count_F = 0
    count_T = 0
    count_CE = 0
    count_blnk = 0
    next(reader, None)  # skip the header row
    next(reader, None)  # skip the second header row
    for row in reader:
        # Check if the row has enough columns
        if len(row) != 20:
            print("Invalid csv format. Please check the number of columns in each row.")
            exit()
        # print(row[19])
        # Insert data into the card_activation table
        fileNumber = row[0] if row[0] else 'N/A'
        studentId = row[1] if row[1] else 'N/A'
        name = row[2] if row[2] else 'N/A'
        program = row[3] if row[3] else 'N/A'
        studentClass = row[19] if row[19] else 'N/A'
        storageLocation = row[18] if row[18] else 'N/A'
        card_activation_data = (fileNumber, studentId, ACTIVE_STATUS, name, program, studentClass, storageLocation)
        cursor.execute(card_activation_query, card_activation_data)
        if cursor.rowcount == 1:
            # Increment row count if the insert was successful
            row_count += 1
        last_inserted_id = cursor.lastrowid

        # Query for requirements if the active status cell is empty
        if(row[19]==''):
            if(row[6]==''):
                for i in range(11,18):
                    cell_value = 1 if row[i] else 0
                    note = row[i].split('/', 1)[-1].strip() if row[i] else ''
                    match i:
                        case 11:
                            user_requirements_data = (last_inserted_id, 4, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data)
                        case 12:
                            user_requirements_data = (last_inserted_id, 9, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data) 
                        case 13:
                            user_requirements_data = (last_inserted_id, 6, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data) 
                        case 14:
                            user_requirements_data = (last_inserted_id, 5, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data) 
                        case 15:
                            user_requirements_data = (last_inserted_id, 3, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data) 
                        case 16:
                            user_requirements_data = (last_inserted_id, 2, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data)
                        case 17:
                            user_requirements_data = (last_inserted_id, 7, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data)
            else:
                for i in range(4,11):
                    cell_value = 1 if row[i] else 0
                    note = row[i].split('/', 1)[-1].strip() if row[i] else ''
                    match i:
                        case 4:
                            user_requirements_data = (last_inserted_id, 0, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data)
                        case 5:
                            user_requirements_data = (last_inserted_id, 1, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data) 
                        case 6:
                            user_requirements_data = (last_inserted_id, 6, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data) 
                        case 8:
                            user_requirements_data = (last_inserted_id, 3, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data) 
                        case 9:
                            user_requirements_data = (last_inserted_id, 2, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data) 
                        case 10:
                            user_requirements_data = (last_inserted_id, 7, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data)
            count_blnk += 1
        # Query for requirements if the active status is not empty
        else:
            if(row[19].strip() == 'F'):
                for i in range(4,11):
                    cell_value = 1 if row[i] else 0
                    note = row[i].split('/', 1)[-1].strip() if row[i] else ''
                    match i:
                        case 4:
                            user_requirements_data = (last_inserted_id, 0, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data)
                        case 5:
                            user_requirements_data = (last_inserted_id, 1, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data) 
                        case 6:
                            user_requirements_data = (last_inserted_id, 6, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data) 
                        case 8:
                            user_requirements_data = (last_inserted_id, 3, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data) 
                        case 9:
                            user_requirements_data = (last_inserted_id, 2, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data) 
                        case 10:
                            user_requirements_data = (last_inserted_id, 7, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data)
                count_F += 1

            if(row[19].strip() == 'T'):
                for i in range(11,18):
                    cell_value = 1 if row[i] else 0
                    note = row[i].split('/', 1)[-1].strip() if row[i] else ''
                    match i:
                        case 11:
                            user_requirements_data = (last_inserted_id, 4, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data)
                        case 12:
                            user_requirements_data = (last_inserted_id, 9, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data) 
                        case 13:
                            user_requirements_data = (last_inserted_id, 6, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data) 
                        case 14:
                            user_requirements_data = (last_inserted_id, 5, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data) 
                        case 15:
                            user_requirements_data = (last_inserted_id, 3, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data) 
                        case 16:
                            user_requirements_data = (last_inserted_id, 2, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data)
                        case 17:
                            user_requirements_data = (last_inserted_id, 7, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data)
                count_T += 1

            if(row[19].strip() == 'CE'):
                for i in range(11,18):
                    cell_value = 1 if row[i] else 0
                    note = row[i].split('/', 1)[-1].strip() if row[i] else ''
                    match i:
                        case 11:
                            user_requirements_data = (last_inserted_id, 4, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data)
                        case 12:
                            user_requirements_data = (last_inserted_id, 9, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data) 
                        case 13:
                            user_requirements_data = (last_inserted_id, 6, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data) 
                        case 14:
                            user_requirements_data = (last_inserted_id, 5, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data) 
                        case 15:
                            user_requirements_data = (last_inserted_id, 3, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data) 
                        case 16:
                            user_requirements_data = (last_inserted_id, 2, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data)
                        case 17:
                            user_requirements_data = (1, 7, cell_value, note)
                            cursor.execute(user_requirements_query, user_requirements_data)
                count_CE += 1
        
        # Insert data into the user_requirements table
        # for i in range(4, 15):
        #     cell_value = 1 if row[i] else 0
        #     user_requirements_data = (last_inserted_id, i-4, cell_value)
        #     cursor.execute(user_requirements_query, user_requirements_data)

# Commit changes to the database
cnx.commit()

# Close database connection
cursor.close()
cnx.close()

print(f"Rows where row 19 is F: {count_F}")
print(f"Rows where row 19 is T: {count_T}")
print(f"Rows where row 19 is CE: {count_CE}")
print(f"Rows where row 19 is blank: {count_blnk}")
print(f"Successfully inserted {row_count} rows into the database.")
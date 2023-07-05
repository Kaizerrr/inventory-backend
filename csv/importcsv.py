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
import datetime

# Establish database connection
cnx = mysql.connector.connect(
    user="root",
    password="",
    host="localhost",
    port=3306,
    database="laravel",
    charset="utf8mb4",
)
cursor = cnx.cursor()
ACTIVE_STATUS = 0

# Prepare MySQL queries
card_activation_query = "INSERT INTO students (studentId, activeStatus, name, program, studentClass, storageLocation) VALUES (%s, %s, %s, %s, %s, %s)"
user_requirements_query = "INSERT INTO student_requirements (student_id, requirement_id, submitted, note) VALUES (%s, %s, %s, %s)"
log_query = "INSERT INTO logs (user_id, student_id, endpoint, action, created_at, updated_at, description) VALUES (%s, %s, %s, %s, %s, %s, %s)"


def create_log_query(student_id, name):
    current_time = datetime.datetime.now().strftime(
        "%Y-%m-%d %H:%M:%S"
    )  # Convert current time to string
    log_data = (
        1,
        student_id,
        "/import",
        "imported",
        current_time,
        current_time,
        f"Imported {name}",
    )
    cursor.execute(log_query, log_data)


# Load data from CSV file
encodings = ["utf-8", "latin-1", "iso-8859-1"]  # Try different encodings

for encoding in encodings:
    try:
        with open("IA E-F.csv", "r", encoding=encoding) as file:
            reader = csv.reader(file)
            row_count = 0  # initialize row count
            count_F = 0
            count_T = 0
            count_CE = 0
            count_blnk = 0
            count_others = 0
            next(reader, None)  # skip the header row
            next(reader, None)  # skip the second header row
            for row in reader:
                # Check if the row has enough columns
                if len(row) != 20:
                    print("Invalid csv format. Please check the number of columns in each row.")
                    exit()

                studentId = row[1].strip() if row[1] else "N/A"
                name = row[2].strip() if row[2] else "N/A"
                program = row[3].strip() if row[3] else "N/A"
                studentClass = row[19].strip() if row[19] else "N/A"
                storageLocation = row[18].strip() if row[18] else "N/A"
                card_activation_data = (
                    studentId,
                    ACTIVE_STATUS,
                    name,
                    program,
                    studentClass,
                    storageLocation,
                )
                cursor.execute(card_activation_query, card_activation_data)
                if cursor.rowcount == 1:
                    # Increment row count if the insert was successful
                    row_count += 1
                last_inserted_id = cursor.lastrowid
                create_log_query(last_inserted_id, name)

                # Extract values from specific ranges
                range1_values = [row[i].strip() if row[i] else "N/A" for i in range(11, 18)]
                range2_values = [row[i].strip() if row[i] else "N/A" for i in range(4, 11)]
                print(name, "range 1", range1_values)
                print(name, "range 2", range2_values)

                # Determine the appropriate requirement values based on the extracted ranges
                if not all(value == "N/A" for value in range1_values):
                    for i in range(11, 18):
                        cell_value = 1 if range1_values[i - 11] != "N/A" else 0
                        note = (
                            range1_values[i - 11].split("/", 1)[-1].strip()
                            if range1_values[i - 11]
                            else ""
                        )
                        # Execute user_requirements_query with the appropriate values
                        match i:
                            case 11:
                                user_requirements_data = (last_inserted_id, 3, cell_value, note)
                                cursor.execute(user_requirements_query, user_requirements_data)
                                print("T", name, user_requirements_data)
                            case 12:
                                user_requirements_data = (last_inserted_id, 8, cell_value, note)
                                cursor.execute(user_requirements_query, user_requirements_data)
                                print("T", name, user_requirements_data)
                            case 13:
                                user_requirements_data = (last_inserted_id, 5, cell_value, note)
                                cursor.execute(user_requirements_query, user_requirements_data)
                                print("T", name, user_requirements_data)
                            case 14:
                                user_requirements_data = (last_inserted_id, 4, cell_value, note)
                                cursor.execute(user_requirements_query, user_requirements_data)
                                print("T", name, user_requirements_data)
                                print("T", name, user_requirements_data)
                            case 15:
                                user_requirements_data = (last_inserted_id, 2, cell_value, note)
                                cursor.execute(user_requirements_query, user_requirements_data)
                                print("T", name, user_requirements_data)
                            case 16:
                                user_requirements_data = (last_inserted_id, 1, cell_value, note)
                                cursor.execute(user_requirements_query, user_requirements_data)
                                print("T", name, user_requirements_data)
                            case 17:
                                user_requirements_data = (last_inserted_id, 6, cell_value, note)
                                cursor.execute(user_requirements_query, user_requirements_data)
                                print("T", name, user_requirements_data)
                    count_T += 1
                elif not all(value == "N/A" for value in range2_values):
                    for i in range(4, 11):
                        cell_value = 1 if range2_values[i - 4] != "N/A" else 0
                        note = (
                            range2_values[i - 4].split("/", 1)[-1].strip()
                            if range2_values[i - 4]
                            else ""
                        )
                        # Execute user_requirements_query with the appropriate values
                        match i:
                            case 4:
                                user_requirements_data = (
                                    last_inserted_id,
                                    10,
                                    cell_value,
                                    note,
                                )
                                cursor.execute(user_requirements_query, user_requirements_data)
                            case 5:
                                user_requirements_data = (last_inserted_id, 0, cell_value, note)
                                cursor.execute(user_requirements_query, user_requirements_data)
                            case 6:
                                user_requirements_data = (last_inserted_id, 5, cell_value, note)
                                cursor.execute(user_requirements_query, user_requirements_data)
                            case 8:
                                user_requirements_data = (last_inserted_id, 2, cell_value, note)
                                cursor.execute(user_requirements_query, user_requirements_data)
                            case 9:
                                user_requirements_data = (last_inserted_id, 1, cell_value, note)
                                cursor.execute(user_requirements_query, user_requirements_data)
                            case 10:
                                user_requirements_data = (last_inserted_id, 6, cell_value, note)
                                cursor.execute(user_requirements_query, user_requirements_data)
                    count_F += 1
                elif any(value == "C.E." for value in range1_values):
                    for i in range(11, 18):
                        if range1_values[i - 11] == "C.E.":
                            cell_value = 1
                            note = range1_values[i - 11].split("/", 1)[-1].strip()
                        else:
                            cell_value = 0
                            note = ""
                        # Execute user_requirements_query with the appropriate values
                        match i:
                            case 11:
                                user_requirements_data = (last_inserted_id, 3, cell_value, note)
                                cursor.execute(user_requirements_query, user_requirements_data)
                                print("CE", name, user_requirements_data)
                            case 12:
                                user_requirements_data = (last_inserted_id, 8, cell_value, note)
                                cursor.execute(user_requirements_query, user_requirements_data)
                                print("CE", name, user_requirements_data)
                            case 13:
                                user_requirements_data = (last_inserted_id, 5, cell_value, note)
                                cursor.execute(user_requirements_query, user_requirements_data)
                                print("CE", name, user_requirements_data)
                            case 14:
                                user_requirements_data = (last_inserted_id, 4, cell_value, note)
                                cursor.execute(user_requirements_query, user_requirements_data)
                                print("CE", name, user_requirements_data)
                                print("CE", name, user_requirements_data)
                            case 15:
                                user_requirements_data = (last_inserted_id, 2, cell_value, note)
                                cursor.execute(user_requirements_query, user_requirements_data)
                                print("CE", name, user_requirements_data)
                            case 16:
                                user_requirements_data = (last_inserted_id, 1, cell_value, note)
                                cursor.execute(user_requirements_query, user_requirements_data)
                                print("CE", name, user_requirements_data)
                            case 17:
                                user_requirements_data = (last_inserted_id, 6, cell_value, note)
                                cursor.execute(user_requirements_query, user_requirements_data)
                                print("CE", name, user_requirements_data)
                    count_CE += 1
                elif all(value == "N/A" for value in range1_values) and all(
                    value == "N/A" for value in range2_values
                ):
                    count_blnk += 1
                else:
                    count_others += 1

        cnx.commit()  # Commit changes to the database
        print("Data imported successfully!")
        print("Total Rows:", row_count)
        print("Total T:", count_T)
        print("Total F:", count_F)
        print("Total C.E.:", count_CE)
        print("Total Blank:", count_blnk)
        print("Total Others:", count_others)

        break  # Break the loop if successful

    except UnicodeDecodeError:
        print(f"Failed to decode the CSV file using encoding: {encoding}")

# Close the database connection
cursor.close()
cnx.close()

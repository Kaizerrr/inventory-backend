import chardet

# Read the contents of the CSV file as binary data
with open('IA Active.csv', 'rb') as file:
    data = file.read()

# Detect the character encoding of the data
result = chardet.detect(data)
encoding = result['encoding']

print(f"The character encoding of the CSV file is: {encoding}")

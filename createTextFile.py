import itertools

INPUT = "data/test-data/SNPs10000.txt"
OUTPUT = open("data/test-data/SNPs100.txt", "w+")


with open(INPUT, "r") as text_file:
    for line in itertools.islice(text_file, 0, 100):
        OUTPUT.write(line)


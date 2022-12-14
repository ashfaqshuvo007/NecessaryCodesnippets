INPUT = "data/test-data/SNPs100.txt"

with open(INPUT, 'r') as fp:
    x = len(fp.readlines())
    print('Total lines:', x)


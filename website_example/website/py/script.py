#!/usr/share/gcc-4.9 python
import sys
import scipy.io
import numpy as np
folder = sys.argv[1]
_file = sys.argv[2]
file_path = folder + "/" + _file
data = scipy.io.loadmat(file_path)

for i in data:
	if '__' not in i and 'readme' not in i:
		np.savetxt((folder+"/"+i+".txt"),data[i],delimiter='\n')

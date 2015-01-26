
import sys
import re
from time import sleep


class GcodeTempUpdate():
    
    def __init__(self, sourceFile, targetFile = None):
        self.sourceFile = sourceFile
        if targetFile:
            self.targetFile = targetFile
        else:
            self.targetFile = sourceFile
        self.file = open(sourceFile, 'rU')
        self.lineList = self.file.readlines()
        self.file.close()
        self.scanFile()
        
    
    def scanFile(self):
#         print len(self.file.read())
        lineList = self.lineList
        
        testCodes = re.compile(';.*')
        moveCodes = re.compile('(?:G0|G1)\s')
        bedTemp = re.compile('(?:M140|M190)\s')
        extTemp = re.compile('(?:M104|M109)\s')
        zCode = re.compile('Z(\d+\.?\d*)')
        eCode = re.compile('E(\d+\.?\d*)')
        srCode = re.compile('(S|R)(\d+\.?\d*)')
        data = self.data = dict()
#         data = self.data
        
#         print self.file.
        lineCount = -1
        for line in lineList:
            lineCount += 1
#             print 'tell:', self.file.tell()
            s = re.search(';', line)
            if s:
                gData = line[:s.start()]
                gComment = line[s.start():]
#                 print gComment
            else:
                gData = line
                gComment = ''
                
            if moveCodes.search(gData):
                
                reZ = zCode.search(gData)
                if reZ:
                    
#                     print line, 'Z:', reZ.group(1)
                    
                    if not data.has_key('firstLayerheight'): 
                        data['firstLayerheight'] = float(reZ.group(1))
                        data['firstLayerStart'] = lineCount
                    elif  float(reZ.group(1)) > data['firstLayerheight'] and not data.has_key('layerheight'):
                        data['layerheight'] = float(reZ.group(1)) - data['firstLayerheight']
                        data['secondLayerStart'] = lineCount
                    
                reE = eCode.search(gData)    
                if reE:
                    data['lastExtrudeLine'] = lineCount
                        
            
            if  data.has_key('firstLayerStart') and not data.has_key('secondLayerStart'):
                if bedTemp.search(gData):
                    data['secondLayerBedTempLine'] = lineCount
#                     print line,  
                
                if  extTemp.search(gData):
                    data['secondLayerExtTempLine'] = lineCount
#                     print line,
                
            
       
        
        return data

    def updateTemp(self, bedTempFirst, bedTemp, extTempFirst, extTemp):
        lineList = self.lineList
        updList = self.updList = list()
        
        reMoveCodes = re.compile('(?:G0|G1)\s')
        reBedTemp = re.compile('(?:M140|M190)\s')
        reExtTemp = re.compile('(?:M104|M109)\s')
        reZCode = re.compile('Z(\d+\.?\d*)')
        reECode = re.compile('E(\d+\.?\d*)')
        reSrCode = re.compile('(S|R)(\d+\.?\d*)')
        reComment = re.compile(';')
        data = self.data
        
        lineCount = -1
        for line in lineList:
            lineCount += 1
            
            reCommentMatch = reComment.search(line)
            if reCommentMatch:
                gData = line[:reCommentMatch.start()]
                gComment = line[reCommentMatch.start():]
               
#                 print gComment

            else:
                gData = line
                gComment = ''
                
            reBedTempMatch = reBedTemp.search(gData)
            reExtTempMatch = reExtTemp.search(gData)
            
            if reBedTempMatch and lineCount < data['lastExtrudeLine']:
                reSrCodeMatch = reSrCode.search(gData)
                if data.has_key('secondLayerBedTempLine') and lineCount < data['firstLayerStart']:
                    updList.append(gData.replace(reSrCodeMatch.group(0), reSrCodeMatch.group(1) + str(bedTempFirst)) + gComment)  #write bedTemp firstlayer
                else:
                    updList.append(gData.replace(reSrCodeMatch.group(0), reSrCodeMatch.group(1) + str(bedTemp)) + gComment) #write other bedTemp
                
            elif reExtTempMatch and lineCount < data['lastExtrudeLine']:
                reSrCodeMatch = reSrCode.search(gData)
                if data.has_key('secondLayerBedTempLine') and lineCount < data['firstLayerStart']:
                    updList.append(gData.replace(reSrCodeMatch.group(0), reSrCodeMatch.group(1) + str(extTempFirst)) + gComment) #write extTemp First layer
                else :
                    updList.append(gData.replace(reSrCodeMatch.group(0), reSrCodeMatch.group(1) + str(extTemp)) + gComment) #write other extTemp
                
            else:
                updList.append(line)  #copy line to new file
        
        
        
        writeFile = open(self.targetFile, 'w')
            
        writeFile.writelines(updList)
        writeFile.flush()
        writeFile.close()
        


def main():
    if len(sys.argv) < 6:
        print "usage: %s bedTempFirst bedTemp extTempFirst extTemp sourceFilename.gcode [targetFilename.gcode]" % sys.argv[0]
        return
    if len(sys.argv) < 7:
        updater = GcodeTempUpdate(sys.argv[5])
    else:
        updater = GcodeTempUpdate(sys.argv[5], sys.argv[6])
        

        
    
    updater.updateTemp(sys.argv[1], sys.argv[2], sys.argv[3], sys.argv[4])
   
#     print 'data: ', updater.updList
    
    
if __name__ == '__main__':
    main()

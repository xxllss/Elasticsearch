"""
Created on 2017年11月21日

@author: xxllss
"""
from elasticsearch import Elasticsearch

class SaveData():
    def __init__(self):
        self.es=Elasticsearch([
                'http://username:password@192.168.1.22:9200/'
            ])

    def saveData(self,data):
        for index,body in data.items():
            doc_id = index+body['TIME']
            return self.es.index(index, index, body,doc_id)

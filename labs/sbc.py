import random
import requests
import time
import RPi.GPIO as GPIO 
import datetime
import cv2

#Usa a biblioteca opencv-python para capturar imagens usando o protocolo http
cap = cv2.VideoCapture("http://10.20.228.22:4747/video")
#nome do veículo a controlar
veiculo = "veiculolabs"

#API URL
url = "https://iot.dei.estg.ipleiria.pt/ti/g168/api/api.php"
#URL para pedidos GET de valor
urlGet = url+"?veiculo="+veiculo+"&valor="

#envia um pedido POST para atualizar um componente na API
def post2API(nome, valor):
    agora = datetime.datetime.now()     
    payload = {'nome': nome, 'veiculo': veiculo , 'valor': valor , 'hora': agora.strftime("%Y-%m-%d %H:%M:%S")}
    r = requests.post(url, data=payload)
    if(r.status_code != 200):
        print(r.text)

#Configuração do atuador e sensores
GPIO.setmode(GPIO.BCM)
ventoinhaPin = 2
butaoPortasPin = 3
butaoLuzesPin = 4
GPIO.setup(ventoinhaPin, GPIO.OUT)
GPIO.setup(butaoPortasPin, GPIO.IN)
GPIO.setup(butaoLuzesPin, GPIO.IN)

print ("--- Prima CTRL + C para terminar ---")

try:
	while True:
		#Captura uma imagem do serviço configurado e atualiza a imagem da webcam usando api/upload.php
		ret, frame = cap.read()
		if ret:
			cv2.imwrite('captura.jpg', frame)
		files = {'imagem': open('captura.jpg', 'rb')}
		payload = {'veiculo': veiculo}
		r = requests.post("https://iot.dei.estg.ipleiria.pt/ti/g168/api/upload.php", files=files, data=payload)

		#Obtem o estado defenido na dashboard para as portas
		request = requests.get(urlGet+'portas')
		if(request.status_code == 200):
			valorPortasDashboard = int(request.text)
			#caso seja 1, então o utilizador pretende fechar as portas.
			#	Obtem o valor do timestamp registado pelos sensores de movimento
			#e avalia se pode fechar as portas atualizando a API
			if(valorPortasDashboard==1):
				request = requests.get(urlGet+'movimento')
				timestamp = int(request.text)
				if(timestamp <int(time.time())-5):
					post2API('portas', "2")
					print("Fechar Portas!")

			#Se o butão de controlo de portas estiver pressionado atualiza o valor
			if not(GPIO.input(butaoPortasPin)):
				if(valorPortasDashboard==0):
					post2API('portas', "1")
					print("Iniciar Fecho!")
				else:
					post2API('portas', "0")
					print("Iniciar Abertura!")

		#Se o butão de controlo de luzes estiver pressionado 
		if not(GPIO.input(butaoLuzesPin)):
			#obtem o valor da controlar de iluminacao e atualiza o sistema
			request = requests.get(urlGet+'iluminacao')
			valorIluminacaoDashboard = int(request.text)
			if(valorIluminacaoDashboard==0):
				post2API('iluminacao', "1")
				print("Luz Baixa")
			elif(valorIluminacaoDashboard==1):
				post2API('iluminacao', "2")
				print("Luz Alta")
			elif(valorIluminacaoDashboard==2):
				post2API('iluminacao', "0")
				print("Luz Desligada")

		#Obtem o valor do sensor de temperatura
		request = requests.get(urlGet+'temperatura')
		if(request.status_code == 200):
			valueT = float(request.text)
		
		#Obtem o valor do sensor de humidade
		request = requests.get(urlGet+'humidade')
		if(request.status_code == 200):
			valueH = float(request.text)
		
		#Obtem os parâmetros do controlador de ventoinha
		request = requests.get(urlGet+'controlador')
		valores_controlador = request.text.split(';')
		tempLimite = int(valores_controlador[0])
		humiLimite = int(valores_controlador[1])
		#Controla a ventoinha de acordo com os valores obtidos
		if((valueV ==1) and (tempLimite >= valueT and humiLimite >= valueH)):
			valueV=0
			print("Ventoinha desligada")
			GPIO.output(ventoinhaPin, GPIO.LOW)
			post2API('ventoinha', valueV)
		elif((valueV == 0) and (tempLimite < valueT or humiLimite < valueH)):
			valueV=1
			print("Ventoinha ligada")
			GPIO.output(ventoinhaPin, GPIO.HIGH)
			post2API('ventoinha', valueV)
	
	time.sleep(2)   

except Exception as e:
	#captura todos os erros
	print("Erro inesperado:", e)
except KeyboardInterrupt:
	print("\Progama terminado pelo utilizador")
finally:
	GPIO.cleanup()
	print("Fim do programa")

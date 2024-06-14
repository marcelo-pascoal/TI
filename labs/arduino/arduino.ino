#include <WiFi101.h>
#include <ArduinoHttpClient.h>
#include <TimeLib.h>
#include <DHT.h>
#include <WiFiUdp.h>
#include <NTPClient.h>

/**************** CLIENT CONFIG ***************/
char ssid[] = "labs";
char pass[] = "1nv3nt@r2023_IPLEIRIA";
int keyIndex = 0;int status = WL_IDLE_STATUS;
char URL[] = "iot.dei.estg.ipleiria.pt";
int PORTO = 80; // ou outro porto que esteja definido no servidor
String resposta = "";
int http_status = 0;
bool safe = false;

WiFiClient clienteWifi;
HttpClient clienteHTTP = HttpClient(clienteWifi, URL, PORTO);
WiFiUDP clienteUDP;
char NTP_SERVER[] = "ntp.ipleiria.pt";
NTPClient clienteNTP(clienteUDP, NTP_SERVER, 3600);

/***************** SENSOR DHT *****************/
#define DHTPIN 0          // Pin Digital onde está ligado o sensor
#define DHTTYPE DHT11     // Tipo de sensor DHT
DHT dht(DHTPIN, DHTTYPE); // Instanciar e declarar a class DHT

/************* ATUADOR ILUMINAÇÃO *************/
// NíVEL 1 - LED_BUILTIN;
int LIGHTPIN2 = 13;

/********* SENSORES LUGARES  (BUTTON) *********/
int lugaresPin[3] = {8, 9, 10};
const char lugaresNome[3][10] = {"lugar-0-0", "lugar-1-0", "lugar-1-1"};
//valor iniciar a -1 para sincronizar com a API ao iniciar
int lugaresValor[3] = {-1, -1, -1};

// Nome do Veículo
String nomeVeiculo = "veiculolabs";

int valueL = 0;
float valueT = 0;
float valueH = 0;

// Variavel para leitura dos sensores de lugar
int val = 0;
int i = 0;

void setup()
{
  Serial.begin(115200);
  while (!Serial);

  //Configuração dos atuadores de iluminacao
  pinMode(LED_BUILTIN, OUTPUT);
  pinMode(LIGHTPIN2, OUTPUT);

  //Configuração dos sensores de lugar
  for (int i = 0; i < 3; i++)
  {
    pinMode(lugaresPin[i], INPUT);
  }

  WiFi.begin(ssid, pass);
  while (WiFi.status() != WL_CONNECTED){
    Serial.println(".");
    delay(500);
  }

  Serial.print(" Endereço IP: ");
  Serial.println((IPAddress)WiFi.localIP());
  Serial.print("máscara de rede: ");
  Serial.println((IPAddress)WiFi.subnetMask());
  Serial.print("Endereço IP do Default Gateway: ");
  Serial.println((IPAddress)WiFi.gatewayIP());
  Serial.print("potência de Sinal: ");
  Serial.println(WiFi.RSSI());

  dht.begin();
}

void loop()
{
  // Lê os valores dos sensores de humidade de temperatura
  float temperatura = round(dht.readTemperature() * 10) / 10.0;
  float humidade = round(dht.readHumidity() * 10) / 10.0;
  char datahora[20];
  // Caso ocorram alterações atualiza a API e o valor local
  if (valueT != temperatura)
  {
    valueT = temperatura;
    update_time(datahora);
    post2API("temperatura", String(temperatura), datahora);
  }
  if (valueH != humidade)
  {
    valueH = humidade;
    update_time(datahora);
    post2API("humidade", String(humidade), datahora);
  }
  
  // Pede à API o valor definido pelo utilizador para o atuador de iluminação
  clienteHTTP.get("https://iot.dei.estg.ipleiria.pt/ti/g168/api/api.php?valor=iluminacao&veiculo=" + nomeVeiculo);
  http_status = clienteHTTP.responseStatusCode();
  resposta = clienteHTTP.responseBody();
  int res = resposta.toInt();
  
  if (http_status == 200)
  {
    // Caso o pedido tenha sido bem sucedido
    if (valueL != res)
    {
      // Caso o valor pretendido seja diferente do atual, atualiza os atuadores
      Serial.println("Nível de Iluminação: " + resposta);
      Serial.println("valor:" + String(res));
      switch (res)
      {
      case (0):
        valueL=0;
        digitalWrite(LED_BUILTIN, LOW);
        digitalWrite(LIGHTPIN2, LOW);
        break;
      case (1):
        valueL=1;
        digitalWrite(LED_BUILTIN, HIGH);
        digitalWrite(LIGHTPIN2, LOW);
        break;
      case (2):
        valueL=2;
        digitalWrite(LED_BUILTIN, HIGH);
        digitalWrite(LIGHTPIN2, HIGH);
        break;
      }
    }
  }

  // Verifica os sensores de lugar
  for (int i = 0; i < 3; i++)
  {
    val = digitalRead(lugaresPin[i]);
    // caso ocorram alteraçoes atualiza a API e o valor local
    if (val != lugaresValor[i])
    {
      lugaresValor[i] = val;
      update_time(datahora);
      post2API(lugaresNome[i], String(val), datahora);
    }
  }
  delay(2000);
}

/*
 * Efetua um pedido POST para atualizar um determinado sensor
 */
int post2API(String nome, String valor, String hora){

  String body = "nome="+nome+"&valor="+valor+"&hora="+hora+"&veiculo="+nomeVeiculo;

  String URLPath = "/ti/g168/api/api.php";
  String contentType = "application/x-www-form-urlencoded";

  clienteHTTP.post(URLPath, contentType, body);
  while(clienteHTTP.connected()){
    if (clienteHTTP.available()){
      int responseStatusCode = clienteHTTP.responseStatusCode();
      String responseBody = clienteHTTP.responseBody();
      if(http_status== 200){
        Serial.println("Status Code: "+String(responseStatusCode)+" Resposta: "+responseBody);
      }
    }
  }
}

/*
 * Função para criação de timestamp
 */
void update_time(char *datahora)
{
  clienteNTP.update();
  unsigned long epochTime = clienteNTP.getEpochTime();
  sprintf(datahora, "%02d-%02d-%02d %02d:%02d:%02d", year(epochTime), month(epochTime), day(epochTime), hour(epochTime), minute(epochTime), second(epochTime));
}
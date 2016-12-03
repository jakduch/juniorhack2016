#define cit 500
#define s 500
#define l 1000
void setup() {
  Serial.begin(9600);
}

void loop() {
  if (analogRead() > cit) {
    delay(s);
    if (analogRead() < 750) {
      delay(s);
      if (analogRead() > cit) {
        delay(s);
        if (analogRead() < 500) {

        }
      }
    }
  }


  Serial.println(analogRead(0));
}

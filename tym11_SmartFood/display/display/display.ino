#define cit 880
#define s 500
boolean SEND = 1;
boolean data[48];
#include <LiquidCrystal.h>
LiquidCrystal lcd(12, 11, 5, 4, 3, 2);

void setup() {
  Serial.begin(9600);
  lcd.begin(16, 2);
  lcd.write("...");
  pinMode(13, OUTPUT);
}

void loop() {
  if (SEND == 1) {
    Serial.println("123");
    delay(1000);
  }
  if (Serial.available()) {
    lcd.clear();
    delay(100);
    lcd.setCursor(0, 0);
    for (byte i = 0; i < 16; i++) {
      lcd.write(Serial.read());
    }
    lcd.setCursor(0, 1);
    for (byte i = 0; i < 16; i++) {
      lcd.write(Serial.read());
    }
    for (; Serial.available();) {
      SEND = 0;
      Serial.read();
      digitalWrite(13, 1);
    }
  }
  if (analogRead(0) < cit && SEND == 0) {
    delay(s + 100);
    //Serial.println("if0ok");
    if (analogRead(0) > cit) {
      delay(s);
     // Serial.println("if1ok");
      if (analogRead(0) < cit) {
        delay(s);
       // Serial.println("if2ok");
        if (analogRead(0) > cit) {
          for (byte i = 0; i < 16; i++) { //prvni cteni
            delay(s);
            if (analogRead(0) < cit) {
              data[i] = 1;
            }
            else {
              data[i] = 0;
            }
          }
          for (byte k = 0; k < 16; k++) { //vypis
            Serial.print(data[k]);
          }
          for (byte k = 0; k < 16; k++) { //vypis
            Serial.print(data[k]);
          }
          Serial.println();
        }
      }
    }
  }
}

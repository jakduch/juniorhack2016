boolean SEND = 1;
#include <LiquidCrystal.h>
LiquidCrystal lcd(12, 11, 5, 4, 3, 2);

void setup() {
  Serial.begin(9600);
  lcd.begin(16, 2);
  lcd.write("...");
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
    for (byte i = 0; Serial.available(); i++) {
      lcd.write(Serial.read());
      //SEND=0;
    }
  }
}


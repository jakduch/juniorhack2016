#include <LiquidCrystal.h>
LiquidCrystal lcd(12, 11, 5, 4, 3, 2);


void setup() {
  lcd.begin(16, 2);
}

void loop() {
  char r1[] = "ahoj";
  char r2[] = "svete";
  LCD(r1, r2);
}
void LCD(char r1[], char r2[]) {
  lcd.setCursor(0, 0);
  lcd.print(r1);
  lcd.setCursor(0, 1);
  lcd.print(r2);
}


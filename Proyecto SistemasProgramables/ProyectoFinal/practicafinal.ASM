List p=16f877A ;list directive to define processor
#include <p16f877A.inc> ;processor specific variable definitions
;***** VARIABLE DEFINITIONS*****
CBLOCK 0x20
w_temp ;variable used for context saving
status_temp ;variable used for context saving
ConteoRetardo
Conteo
CONTEO_RETARDO
DATA_8
MOVE_CURSOR
CUENTA_1
CUENTA_2
CUENTA_3
VAR
ASCII_H
ASCII_M
ASCII_L
HEXADECIMAL
TEMP
KEY
COLUMNA
VAR_PORTC
VAR_AUX
rx_dato
VALUE_ADC
Hex_Asci
canales
DATO_RX
ENDC
;******************************************************
#define RS PORTC, 3
#define RW PORTC, 4
#define E PORTC, 5
;**************************************************************************
;* Macros to select the register bank. Many bank changes can be optimised *
;* when only one STATUS bit changes *
;**************************************************************************
Bank0 MACRO ;macro to select data RAM bank 0
Bcf STATUS,RP0
Bcf STATUS,RP1
ENDM
Bank1 MACRO ;macro to select data RAM bank 1
Bsf STATUS,RP0
Bcf STATUS,RP1
ENDM
Bank2 MACRO ;macro to select data RAM bank 2
Bcf STATUS,RP0
Bsf STATUS,RP1
ENDM
Bank3 MACRO ;macro to select data RAM bank 3
bsf STATUS,RP0
bsf STATUS,RP1
ENDM
;**********************************************************************
ORG 0x000 ;processor reset vector
Clrf PCLATH ;ensure page bits are cleared
goto main ;go to beginning of program
ORG 0x004 ;interrupt vector location
Movwf w_temp ;save off current W register contents
movf STATUS,w ;move status register into W register
bcf STATUS,RP0 ;ensure file register bank set to 0
movwf status_temp ;save off contents of STATUS register
;isr code can go here or be located as
;--------------------------------------
btfsc PIR1, RCIF ;Verifica si hay un dato en el buffer RX
call RX_Interrupcion ;Brinca a la subrutina
bcf STATUS,RP0 ;ensure file register bank set to 0
;================================================
movf status_temp,w ;retrieve copy of STATUS register
movwf STATUS ;restore pre-isr STATUS register contents
swapf w_temp,f
swapf w_temp,w ;restore pre-isr W register contents
retfie ;return from interrupt



;***********************************************************************
; Código principal *
;***********************************************************************
main ; remaining code goes here
call INIT_PORTS ;Subrutina para inicializar los puertos
call INIT_LCD ;Subrutina para inicializar el LCD
call INIT_RS232
INICIO
call Main_Message
LOOP
call TECLADO ;Rutina para leer el teclado
call LEER_CANAL_0
call LEER_CANAL_1
call LEER_CANAL_2
call LEER_CANAL_3
goto LOOP



    
	return

;************************************************************************
;mensaje inicio
Main_Message
call CLEAR_LCD
movlw MENSAJEBIENV
movwf VAR
call SET_MESSAGE
movlw .5
call Cursor_Linea_2
movlw MENSAJEBIENV2
movwf VAR
call SET_MESSAGE
call Retardo_1s
call CLEAR_LCD

movlw MENU1
movwf VAR
call SET_MESSAGE
call Cursor_Linea_2
movlw MENU2
movwf VAR
call SET_MESSAGE
call Retardo_1s
call CLEAR_LCD

movlw MENU3
movwf VAR
call SET_MESSAGE
call Cursor_Linea_2
movlw MENU4
movwf VAR
call SET_MESSAGE
call Retardo_1s
call CLEAR_LCD

return

;**********************************************************************************
;*****************************************************************
;* Rutina para leer el teclado y checa que el botón fue soltado *
;*****************************************************************

TECLADO
clrf KEY
movlw 0xFF
movwf COLUMNA
bcf STATUS, C
TECLAS
rrf COLUMNA, F
movf COLUMNA,W
movwf PORTB
btfss PORTB, 3
goto SOLTAR_TECLA
incf KEY
btfss PORTB, 2
goto SOLTAR_TECLA
incf KEY
btfss PORTB, 1
goto SOLTAR_TECLA
incf KEY
btfss PORTB, 0
goto SOLTAR_TECLA
btfss COLUMNA, 4
return
incf KEY
goto TECLAS

SOLTAR_TECLA
btfss PORTB, 3
goto $-1
btfss PORTB, 2
goto $-1
btfss PORTB, 1
goto $-1
btfss PORTB, 0
goto $-1

movf KEY,W
bcf STATUS,Z
sublw 0x00
btfss STATUS,Z
goto $+2
goto ACTIVOCAN1


movf KEY,W
bcf STATUS,Z
sublw 0x01
btfss STATUS,Z
goto $+2
goto ACTIVOCAN2

movf KEY,W
bcf STATUS,Z
sublw 0x02
btfss STATUS,Z
goto $+2
goto ACTIVOCAN3

movf KEY,W
bcf STATUS,Z
sublw 0x03
btfss STATUS,Z
goto $+2
goto ACTIVOCAN4

movf KEY,W
bcf STATUS,Z
sublw 0x04
btfss STATUS,Z
goto $+2
goto INICIO
return


;Colocar la bandera para ver si el canal esta activo o no

ACTIVOCAN1
btfss canales,0
goto $+3
bcf canales,0
goto $+2
bsf canales,0
movlw 'P'
call TRANSMITIR
movlw '0'
call TRANSMITIR
return

ACTIVOCAN2
btfss canales,1
goto $+3
bcf canales,1
goto $+2
bsf canales,1
movlw 'P'
call TRANSMITIR
movlw '1'
call TRANSMITIR
return

ACTIVOCAN3
btfss canales,2
goto $+3
bcf canales,2
goto $+2
bsf canales,2
movlw 'P'
call TRANSMITIR
movlw '2'
call TRANSMITIR
return

ACTIVOCAN4
btfss canales,3
goto $+3
bcf canales,3
goto $+2
bsf canales,3
movlw 'P'
call TRANSMITIR
movlw '3'
call TRANSMITIR
return

;*************************************************************************************************
;lectura de cada canal
;*************************************************************************************************


LEER_CANAL_0
movlw 0xC1     ; configuracion para leer canal 0
movwf ADCON0 
call CONVERTIR ;Salta a la rutina de conversión del A/D
movf ADRESH, W
movwf VALUE_ADC
goto MOSTRAR
return

LEER_CANAL_1
movlw 0xC9  ; configuracion para leer canal 1
movwf ADCON0 
call CONVERTIR ;Salta a la rutina de conversión del A/D
movf ADRESH, W
movwf VALUE_ADC
goto MOSTRAR1
return

LEER_CANAL_2
movlw 0xD1   ; configuracion para leer canal 2
movwf ADCON0 
call CONVERTIR ;Salta a la rutina de conversión del A/D
movf ADRESH, W
movwf VALUE_ADC
goto MOSTRAR2
return

LEER_CANAL_3
movlw 0xD9  ; configuracion para leer canal 3
movwf ADCON0 
call CONVERTIR ;Salta a la rutina de conversión del A/D
movf ADRESH, W
movwf VALUE_ADC
goto MOSTRAR3
return

CONVERTIR
movlw 0x0F
movwf VAR
decfsz VAR, F ;Tiempo de retardo para la conversión
goto $-1
clrf ADRESH ;Limpia el registro de resultado del A/D
bsf ADCON0, GO_DONE ;Conversión en progreso
LOOP_AD
btfsc ADCON0, GO_DONE ;Brinca mientras la conversión
goto LOOP_AD ;es completada
bcf ADCON0, ADON ;Modulo de conversión fuera de progreso
return

;***********************************************************************
;Cosas que se ven en pantalla
;***********************************************************************


CANAL1LIMPIO
movlw '0'
call TRANSMITIR
movlw '0'
call TRANSMITIR
movlw '0'
call TRANSMITIR
movlw '0'
call TRANSMITIR
movlw '0'
call TRANSMITIR
goto VACIO

CANAL2LIMPIO
movlw '0'
call TRANSMITIR
movlw '0'
call TRANSMITIR
movlw '0'
call TRANSMITIR
movlw '0'
call TRANSMITIR
movlw '0'
call TRANSMITIR
goto VACIO

CANAL3LIMPIO
movlw '0'
call TRANSMITIR
movlw '0'
call TRANSMITIR
movlw '0'
call TRANSMITIR
movlw '0'
call TRANSMITIR
movlw '0'
call TRANSMITIR
goto VACIO

CANAL4LIMPIO
movlw '0'
call TRANSMITIR
movlw '0'
call TRANSMITIR
movlw '0'
call TRANSMITIR
movlw '0'
call TRANSMITIR
movlw '0'
call TRANSMITIR
goto VACIO


VACIO
movlw '0'
movwf DATA_8
call DATALCD
movlw	' '
movwf DATA_8
call DATALCD
movlw '0'
movwf DATA_8
call DATALCD
movlw '0'
movwf DATA_8
call DATALCD
movlw '0'
movwf DATA_8
call DATALCD
return

MOSTRAR   ;rutina para mostrar el valor del canal 0
movlw .0
call Cursor_Linea_1
btfss canales,0  ;EL CANAL ESTA ACTIVO?
goto $+2         ; si no se va a transmitir ceros y mostrar ceros en display
goto $+3         ;si si va a mostrar lo que tenga VALUE_ADC potenciometro
call CANAL1LIMPIO
return
movf VALUE_ADC, W
call Hex_Ascii
movlw 'A'
call TRANSMITIR
movwf DATA_8
call DATALCD
movlw	' '
movwf DATA_8
call DATALCD
movf ASCII_H,W
addlw 0x30
call TRANSMITIR
movwf DATA_8
call DATALCD
movf ASCII_M,W
addlw 0x30
call TRANSMITIR
movwf DATA_8
call DATALCD
movf ASCII_L,W
addlw 0x30
call TRANSMITIR
movwf DATA_8
call DATALCD
return

MOSTRAR1  ;rutina para mostrar el valor del canal 1
movlw .8
call Cursor_Linea_1
btfss canales,1
goto $+2
goto $+3
call CANAL2LIMPIO
return
movf VALUE_ADC, W
call Hex_Ascii
movlw 'B'
call TRANSMITIR
movwf DATA_8
call DATALCD
movlw	' '
movwf DATA_8
call DATALCD
movf ASCII_H,W
addlw 0x30
call TRANSMITIR
movwf DATA_8
call DATALCD
movf ASCII_M,W
addlw 0x30
call TRANSMITIR
movwf DATA_8
call DATALCD
movf ASCII_L,W
addlw 0x30
call TRANSMITIR
movwf DATA_8
call DATALCD
return

MOSTRAR2  ;rutina para mostrar el valor del canal 2
movlw .0
call Cursor_Linea_2
btfss canales,2
goto $+2
goto $+3
call CANAL3LIMPIO
return
movf VALUE_ADC, W
call Hex_Ascii
movlw 'C'
call TRANSMITIR
movwf DATA_8
call DATALCD
movlw	' '
movwf DATA_8
call DATALCD
movf ASCII_H,W
addlw 0x30
call TRANSMITIR
movwf DATA_8
call DATALCD
movf ASCII_M,W
addlw 0x30
call TRANSMITIR
movwf DATA_8
call DATALCD
movf ASCII_L,W
addlw 0x30
call TRANSMITIR
movwf DATA_8
call DATALCD
return

MOSTRAR3  ;rutina para mostrar el valor del canal 3
movlw .8
call Cursor_Linea_2
btfss canales,3
goto $+2
goto $+3
call CANAL4LIMPIO
return
movf VALUE_ADC, W
call Hex_Ascii
movlw 'D'
call TRANSMITIR
movwf DATA_8
call DATALCD
movlw	' '
movwf DATA_8
call DATALCD
movf ASCII_H,W
addlw 0x30
call TRANSMITIR
movwf DATA_8
call DATALCD
movf ASCII_M,W
addlw 0x30
call TRANSMITIR
movwf DATA_8
call DATALCD
movf ASCII_L,W
addlw 0x30
call TRANSMITIR
movwf DATA_8
call DATALCD
return


;----------------------------------------------
Hex_Ascii
movwf HEXADECIMAL
clrf ASCII_H ;Para BCD D'0'
clrf ASCII_M ;Para BCD D'0'
clrf ASCII_L ;Para BCD D'0'
Centenas
movlw D'100' ;D'100' --> W
subwf HEXADECIMAL,W ;HEXADECIMAL - D'100' --> W
btfss STATUS,C ;ø HEXADECIMAL es menor que D'100' ?
goto Decenas ;Si
movwf HEXADECIMAL ;Salva el resto
incf ASCII_H,F ;Incrementa el contador de centenas BCD
goto Centenas ;Vuelve por otros D'100'
Decenas
movlw D'10' ;D'10' --> W
subwf HEXADECIMAL,W ;HEXADECIMAL - D'10' --> W
btfss STATUS,C ;ø HEXADECIMAL es menor que D'10' ?
goto Unidades ;Si
movwf HEXADECIMAL ;Salva el resto
incf ASCII_M,F ;Incrementa el contador de decenas BCD
goto Decenas ;Vuelve por otros D'10'
Unidades
Movf HEXADECIMAL,W ;El resto pertenece
Movwf ASCII_L ;a las unidades BCD
Clrf HEXADECIMAL ;Pone a cero el registro HEXADECIMAL
return ;Retorna


;******************************************************************************************************
TRANSMITIR
;Primeramente verifica y espera a que el puerto serie este listo para
;transmitir (que tenga espacio en el buffer), esto se puede hacer
;verificando que la bandera de interrupción del modulo este activada
;(nótese que esta bandera opera aun cuando la interrupción esta desactivada)
btfss PIR1, TXIF
goto $-1
;Para enviar el dato, simplemente se escribe el mismo al registro de
;transmision
movwf TXREG
return
;*******************************************************************************************+
INIT_RS232
Bank1 ;Selecciona Banco 1
movlw 0x26 ;Configuración del puerto serie: 8 bits, TX
movwf TXSTA ;habilitada, modo asíncrono y alta velocidad
;(los demás bits desactivan otras
;caracteristicas)
movlw d'25' ;Establece la velocidad a 9600 baudios
movwf SPBRG ;usando un oscilador a 4MHz
bsf PIE1, RCIE ;Habilita la interrupción de recepción de la
;USART
Bank0 ;Selecciona Banco 0
movlw 0x90 ;Configuración del puerto serie: Puerto
;habilitado
movwf RCSTA ;8 bits, recepción continua habilitada
;direcciones (los demás bits no tienen efecto)
clrf PIR1 ;Borra todas las banderas de interrupción
bsf INTCON,GIE ;Desenmascara todas las interrupciones
bsf INTCON,PEIE ;Desenmascara todas las interrupciones por
;perifericos
return

RX_Interrupcion
btfss PIR1, RCIF ;Verifica si hay un dato en el buffer
return ;En caso de no haber, regresa
movf RCREG, W ;si hay dato, lo toma del registro RCREG
movwf DATO_RX ;Hubo dato, así que se guarda
movwf rx_dato

movlw '1' ;Se verifica si se recibió un '1' 
subwf DATO_RX, w
btfsc STATUS, Z
goto ACTIVOCAN1 ;De ser así, va a la rutina de CANAL 1
movlw '2' ;Se verifica si se recibió un '2' 
subwf DATO_RX, w
btfsc STATUS, Z
goto ACTIVOCAN2 ;De ser así, va a la rutina de CANAL 2
movlw '3' ;Se verifica si se recibió un '3'
subwf DATO_RX, w
btfsc STATUS, Z
goto ACTIVOCAN3 ;De ser así, va a la rutina de CANAL 3
movlw '4' ;Se verifica si se recibió un '4'
subwf DATO_RX, w
btfsc STATUS, Z
goto ACTIVOCAN4 ;De ser así, va a la rutina de CANAL 4
return

;***********************************************************************

INIT_PORTS
Bank1 ;Select Bank 1
movlw 0x02 
movwf ADCON1
movlw 0x0F
movwf TRISA
movlw 0x0F ;Configura el puerto B como salida (RB7 - RB4)
movwf TRISB ;y como entradas (RB3 - RB0)
movlw 0x80 ;Configura el puerto C como salida
movwf TRISC
clrf TRISE ; Configura el puerto E como salida
Bank0 ;Select Bank 0
clrf PORTA ;Limpia el puerto A
clrf PORTB ;Limpia el puerto B
clrf PORTC ;Limpia el puerto C
clrf PORTE ;Limpia el puerto E
clrf canales
return

;*************************************************************************
;* Inicialice LCD *
;*************************************************************************
INIT_LCD
bcf RW
bcf RS
bcf E
call Retardo_20ms
movlw 0x03
call Envia_Dato
call Retardo_200micros
movlw 0x03
call Envia_Dato
call Retardo_20micros
movlw 0x03
call Envia_Dato
call Retardo_20micros ;Retardo necesario para simular en PROTEUS
movlw 0x02
call Envia_Dato
call Retardo_20micros ;Retardo necesario para simular en PROTEUS
;******** Modo de operación de 4 bits ***********
movlw 0x28
movwf DATA_8
call COMMAND_DOUBLE
movlw 0x01
movwf DATA_8
call COMMAND_DOUBLE
movlw 0x06
movwf DATA_8
call COMMAND_DOUBLE
movlw 0x0E
movwf DATA_8
call COMMAND_DOUBLE
return
;**************************************************************************
;* Rutina para la configuración del LCD, pone el valor en el puerto *
;**************************************************************************
Envia_Dato
movwf PORTE
bcf PORTA, 5
bsf E
call Retardo_5ms
bcf E
clrf PORTE
bcf PORTA, 5
return
;*************************************************************************
;* Rutina para mandar los datos de 8 bits para el LCD *
;*************************************************************************
COMMAND_DOUBLE
;************* Envía parte alta *****************
bcf E
bcf RS
bcf RW
swapf DATA_8, W
movwf DATA_8
movf DATA_8, W
andlw 0x0F
movwf PORTE
movwf VAR_AUX
btfss VAR_AUX, 3
goto $+3
bsf PORTA, 5
goto $+2
bcf PORTA, 5
bsf E
call Retardo_5ms
bcf E
;*********** Envía parte baja *******************
swapf DATA_8, W
movwf DATA_8
movf DATA_8, W
andlw 0x0F
movwf PORTE
movwf VAR_AUX
btfss VAR_AUX, 3
goto $+3
bsf PORTA, 5
goto $+2
bcf PORTA, 5
bsf E
call Retardo_5ms
bcf E
bsf RW
return
;**************************************************************************
;* Rutina para poner el carácter en el LCD *
;**************************************************************************
DATALCD
;************* Envía parte alta *****************
bsf RS
bcf RW
swapf DATA_8, W
movwf DATA_8
movf DATA_8, W
andlw 0x0F
movwf PORTE
movwf VAR_AUX
btfss VAR_AUX, 3
goto $+3
bsf PORTA, 5
goto $+2
bcf PORTA, 5
bsf E
call Retardo_5ms
bcf E
bcf RS
;*********** Envía parte baja *******************
bsf RS
swapf DATA_8, W
movwf DATA_8
movf DATA_8, W
andlw 0x0F
movwf PORTE
movwf VAR_AUX
btfss VAR_AUX, 3
goto $+3
bsf PORTA, 5
goto $+2
bcf PORTA, 5
bsf E
call Retardo_5ms
bcf E
bcf RS
bsf RW
return
;**************************************************************************
;* Rutina para un tiempo de retardo para iniciar el LCD *
;**************************************************************************
Retardo_200micros ;La llamada "call" 2 ciclos maquina.
nop ;1 ciclo maquina.
movlw d'64' ;1 ciclo maquina. Este es el valor de "K".
goto RetardoMicros ;2 ciclos maquina.
Retardo_20micros ;La llamada "call" 2 ciclos maquina.
movlw d'3' ;1 ciclo maquina. Este es el valor de "K".
goto RetardoMicros ;2 ciclos maquina.
Retardo_20ms ;La llamada "call" 2 ciclos maquina.
movlw d'20' ;1 ciclo maquina. Este es el valor de "M".
goto Retardos_ms ;2 ciclos maquina.
Retardo_5ms ;La llamada "call" 2 ciclos maquina.
movlw d'5' ;1 ciclo maquina. Este es el valor de "M".
goto Retardos_ms ;2 ciclos maquina.
Retardo_1s ;La llamada "call" 2 ciclos maquina.
movlw d'10' ;1 ciclo maquina. Este es el valor de "N".
goto Retardo_1Decima ;2 ciclos maquina.
;*************************************************************************
;* El bloque "RetardoMicros" dura: *
;* 1 + (K-1) + 2 + (K-1)x2 + 2 = (2 + 3K) ciclos m·quina. *
;*En total estas subrutinas duran: *
;* Retardo_200micros: 2 + 1 + 1 + 2 + (2 + 3K) = 200 cm = 200 µs. *
;* Retardo_20micros : 2 + 1 + (2 + 3K) = 20 cm = 20 µs. *
;*(para K= 5 y 4 MHz) *
;*************************************************************************
RetardoMicros
movwf CUENTA_1 ;Aporta 1 ciclo maquina.
micros_Loop
decfsz CUENTA_1,F ;(K-1)x1 cm (no salta) + 2 cm (al saltar).
goto micros_Loop ;Aporta (K-1)x2 ciclos m·quina.
return ;El salto del retorno, 2 ciclos maquina.
;*************************************************************************
;* El bloque "Retardos_ms" dura: *
;* 1 + M + M + KxM + (K-1)xM + Mx2 + (K-1)Mx2 + (M-1) + 2 + (M-1)x2 + 2 =*
;* = (2 + 4M + 4KM) ciclos maquina. Para K=249 y M=1 supone 1002 ciclos *
;* maquina que a 4 MHz son 1002 µs = 1 ms. *
;* En total estas subrutinas duran: *
;* Retardo_20ms :2 + 1 + 2 + (2 + 4M + 4KM) = 20007 cm = 20 ms. (M= 20 *
;* y K=249). *
;* Retardo_5ms :2 + 1 + 2 + (2 + 4M + 4KM) = 5007 cm = 5 ms. (M= 5 *
;* y K=249). *
;*************************************************************************
Retardos_ms
movwf CUENTA_2 ;1 ciclo maquina.
Loop_Externo
movlw d'249' ;Mx1 ciclos maquina. Es el valor de "K".
movwf CUENTA_1 ;Mx1 ciclos maquina.
Loop_Interno
nop ;KxMx1 ciclos maquina.
decfsz CUENTA_1,F ;(K-1)xMx1 cm (no salta) + Mx2 cm(salta).
goto Loop_Interno ;Aporta (K-1)xMx2 ciclos maquina.
decfsz CUENTA_2,F ;(M-1)x1 cm (no salta) + 2 cm (al saltar).
goto Loop_Externo ;(M-1)x2 ciclos maquina.
return ;El salto del retorno 2 ciclos maquina.
;*************************************************************************
;*El bloque "Retardo_1Decima" tarda: *
;* 1 + N + N + MxN + MxN + KxMxN + (K-1)xMxN + MxNx2 + (K-1)xMxNx2 + *
;* + (M-1)xN + Nx2 + (M-1)xNx2 + (N-1) + 2 + (N-1)x2 + 2 = *
;* = (2 + 4M + 4MN + 4KM) ciclos maquina. Para K=249, M=100 y N=1 supone *
;* 100011 ciclos maquina que a 4 MHz son 100011 µs = 100 ms = 0,1 s = 1 *
;* decima de segundo. *
;*************************************************************************
Retardo_1Decima
movwf CUENTA_3 ;1 ciclo maquina.
Loop_Externo2
movlw d'100' ;Nx1 ciclos maquina. Es el valor de "M".
movwf CUENTA_2 ;Nx1 ciclos maquina.
Loop_Externo_1
movlw d'249' ;MxNx1 ciclos maquina. Es el valor de "K".
movwf CUENTA_1 ;MxNx1 ciclos maquina.
Loop_Interno_1
nop ;KxMxNx1 ciclos maquina.
decfsz CUENTA_1,F ;(K-1)xMxNx1 cm (no salta) + MxNx2 cm
;(al saltar).
goto Loop_Interno_1 ;(K-1)xMxNx2 ciclos maquina.
decfsz CUENTA_2,F ;(M-1)xNx1 cm (cuando no salta) + Nx2 cm
;(al saltar).
goto Loop_Externo_1 ;(M-1)xNx2 ciclos maquina.
decfsz CUENTA_3,F ;(N-1)x1 cm (cuando no salta) + 2 cm
;(al saltar).
goto Loop_Externo2 ;(N-1)x2 ciclos maquina.
return ;El salto del retorno,2 ciclos maquina.
;*************************************************************************
;* En total estas subrutinas dura: *
;* Retardo_1s:2 + 1 + 2 + (2 + 4N + 4MN + 4KMN) = 1000047 cm = 1 s. *
;* (N= 10, M=100 y K=249). *
;*************************************************************************
;**************************************************************************
;* Rutina para poner mensajes en el LCD *
;**************************************************************************
;**************************************************************************
;* Rutina para limpiar el LCD *
;**************************************************************************
CLEAR_LCD
movlw 0x01
movwf DATA_8
call COMMAND_DOUBLE
return
;**************************************************************************
;* Rutina para poner el cursor al inicio del LCD *
;**************************************************************************
CURSOR_INIT
movlw 0x80
movwf DATA_8
call COMMAND_DOUBLE
return
;************************************************************************
;* Rutina para poner el cursor en alguna posición de la línea 1 o 2 del *
;* LCD *
;************************************************************************
Cursor_Linea_1 ;Cursor a posición de la línea 1,a partir
Iorlw b'10000000' ;de la dirección 00h de la DDRAM ms el
movwf DATA_8 ;valor del registro W.
goto COMMAND_DOUBLE
Cursor_Linea_2 ;Cursor a posición de la línea 2, a partir
iorlw b'11000000' ;de la dirección 40h de la DDRAM ms el
movwf DATA_8 ;valor del registro W.
goto COMMAND_DOUBLE
;**************************************************************************
;* Estas son las tablas para el LCD. En la posición 400 en la memoria *
;**************************************************************************

SET_MESSAGE
FINAL
movlw HIGH TABLA
movwf PCLATH
movf VAR, W
call TABLA
addlw .00
btfsc STATUS, Z
return
movwf DATA_8
call DATALCD
incf VAR, F
goto FINAL
;**************************************************
;MENU

ORG 0x400
TABLA
movwf PCL
MENSAJEBIENV
retlw 'P'
retlw 'R'
retlw 'O'
retlw 'Y'
retlw 'E'
retlw 'C'
retlw 'T'
retlw 'O'
retlw .0
MENSAJEBIENV2
retlw 'F'
retlw 'I'
retlw 'N'
retlw 'A'
retlw 'L'
retlw .0

MENU1
retlw ' '
retlw 'B'
retlw 't'
retlw 'n'
retlw ' '
retlw '1'
retlw ' '
retlw ' '
retlw 'C'
retlw '1'
retlw .0
MENU2
retlw ' '
retlw 'B'
retlw 't'
retlw 'n'
retlw ' '
retlw '2'
retlw ' '
retlw ' '
retlw 'C'
retlw '2'
retlw .0

MENU3
retlw ' '
retlw 'B'
retlw 't'
retlw 'n'
retlw ' '
retlw '3'
retlw ' '
retlw ' '
retlw 'C'
retlw '3'
retlw .0

MENU4
retlw ' '
retlw 'B'
retlw 't'
retlw 'n'
retlw ' '
retlw '4'
retlw ' '
retlw ' '
retlw 'C'
retlw '4'
retlw .0
END ;directive 'end of program'
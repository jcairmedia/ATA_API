<center>




![imagen](./unnamed.png) 


</center>

|||
|--|--|
|Erika Leonor Basurto Munguia|v 1.0|
|||

# Requerimientos para la instalación de la API

- PHP ^7.2
- Laravel ^7.24
- Mysql ^5 o MariaDB ^10.4


# Instalación de la API

1. Clonar repositorio de github
2. Crear copia del archivo **.env** con claves del entorno correspondiente
3. Descargar los paquete y dependencias usando composer (composer install)
4. Descargar una copia de la base de datos llamada **usercent_ata_qa**
5. Instalar copia de la base de datos
6. Cambiar las credenciales de la base de datos en el archivo **.env** del proyecto
7. Para terminar la configuración de passport seguir la documentacion de Laravel en [https://laravel.com/docs/7.x/passport](https://laravel.com/docs/7.x/passport)
8. Configurar el acceso a los calendarios de Google, seguir la documentación del archivo llamado **Configuracion_calendarios_SMS**
9. Generar JWT de Zoom, ir al archivo llamado **Configuracion_zoom**


#
# Versiones y Dominios
## Versión 1

En el dominio [https://apidev.usercenter.mx/](https://apidev.usercenter.mx/) se encuentra la version 1 estable de la etapa 1 del proyecto.

Esta versión se encuentra en la rama **master** y usa la base de datos llamada  **usercent_atadev**.

Para más información consultar la documentación [https://apidev.usercenter.mx/api/documentation](https://apidev.usercenter.mx/api/documentation)

## Versión 2

En el dominio [https://apiqa.usercenter.mx/](https://apiqa.usercenter.mx/) se encuentra las nuevas funcionalidades definidas en la parte 2 de la etapa 1 y son las siguientes

- Notificaciones
    - Grupales
    - Dirigidas
    - A todos los usuarios registrados

- Consulta de notificaciones del cliente
- Registro de token de dispositivo para envío de notificaciones
- Desvincular token de subscripción para envío de notificaciones

- Eventos: Creación de eventos para cada caso

- Consulta de casos del cliente

- Consulta de contrato del caso 

- Registro y consulta de tarjetas de pago del cliente

- Contratación de paquete con pago con tarjeta previamente registrada

- Correción que solicito **Software ATA** para el **registro de usuarios**
    - Dirección (calle, número interior, número exterior, colonia, municipio, localidad, estado)
    - CURP
    - Código postal

- Consulta de usuarios pendientes de sincronizar por **Software ATA**
- Chequeo de estatus de usuarios leidos por **Software ATA**

- Autenticación obligatoria para contratación de asesoría pagada

- Creación de código de referencia para el registro de usuarios

- Consulta Jurídica (Blog) 



Estas funcionalidades estan definidas en la **rama develop** y la base de datos a la que se conecta es **usercent_ataqa**.

Para la documentación de los endpoints se uso [Swagger](). 
Para actualizar la documentación ejecutar el siguiente comando

`php artisan l5-swagger:generate`


Para más información sobre los nuevos end points que resuelven las funcionalidades consultar la documentacion respectiva.

[https://apiqa.usercenter.mx/api/documentation ](https://apiqa.usercenter.mx/api/documentation )
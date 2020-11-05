# Diveban X Open

Надежная и удобная бан-система для вашего сервера, позволяет банить игроков с указанием причины даже если они покинули сервер. Надежность плагина обеспечивается современными методами и метками для бана игроков.

## Описание
DiveBan X - бан система cs 1.6. Это АМХХ плагин предназначенный для блокировки доступа определенным игрокам к игровому серверу Counter Strike 1.6 на некоторое время, или навсегда. Большинство игроков не смогут обойти бан:
Сменой Steam|IP|Name идентификаторов
Чисткой или Блокировкой конфигов
Переустановкой кс
Используя щиты, гварды, клиент сейверы и тд.
Используя впн, и прочую ересь.

## Преимущества
Представляем Diveban X - новейшая версия плагина diveban, которая приносит большой рефакторинг кода, улучшение стабильности и безопасности, и добавления новых систем для защиты сервера. Новый плагин также поддерживает онлайн бан через сайт. Основные преимущества:

Наличие флага для главного админа | Зам главы
Банить игроков с указанием времени и причины бана.
Редактировать сообщение бана в консоли игрока.
Писать причины и время бана в отдельный .ini файл.
Возможность синхронизации DiveBan с другими бан-системами
Банить игроков, даже если те покинули сервер (оффлайн Бан).
ПОЛНОЕ редактирование надписей в худ\чат, делание скриншотов, freeze игрока, и тд и тп (Все это редактируется в отдельном плагине, с исходником).

## Требования 
Игровой сервер cs 1.6 HLDS|ReHLDS
Mysql база данных
AmxmodX 1.8.2+
Веб-хостинг(для банлиста)

## Установка
Открыть configs/DivebanX/diveban.cfg и прописать ключ.<br>
В том же файле настроить divebans_flag_main(флаг ГЛ админа) и Secret_Key (ключ для очистки банлиста)./li>
В том же файле прописать данные от mysql(csrank_mysql_host, и др)
Настроить остальные конфиги, если необходимо
Скопировать все файлы в addons/amxmomdx/
Прописать плагин вверху списка в configs/plugins.ini
Если что то не так, и плагин не работает, то стоит проверить логи logs\DivebanX\ или написать в консоли сервера amxx plugins и посмотреть ошибку

## Команды плагина
db_fban | amx_ban	<time in min> <#userid or ip> <reason>	Серверная команда бана для античитов
db_unban	<Name or UniqueID or SteamID or Ip>	Серверная команда для разбана
db_clear	Очиста банлиста(Требуется секретный ключ)
db_banmenu | amx_banmenu	Открыть банменю
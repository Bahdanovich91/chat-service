# 💬 Chat Service — WebSocket сервер
**WebSocket сервер для чатов с поддержкой комнат и истории сообщений.**  
**Все соединения работают только через защищенные протоколы (HTTPS/WSS).**

## 🚀 Быстрый старт

**1️⃣  Клонирование репозитория**
```bash
git clone https://github.com/Bahdanovich91/chat-service.git
````

**2️⃣  Генерация SSL-сертификатов**
```bash
mkdir -p docker/ssl
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
-keyout docker/ssl/key.pem \
-out docker/ssl/cert.pem \
-subj "/C=RU/ST=Moscow/L=Moscow/O=ChatService/CN=localhost"
```

**3️⃣  Установка прав доступа**
```bash
chmod 644 docker/ssl/cert.pem
```
```bash
chmod 600 docker/ssl/key.pem
```

**4️⃣  Запуск контейнеров**
```bash
docker compose up -d --build
```

## 📡 Доступ к приложению

> ⚠️ Все соединения работают **только через защищённые протоколы**.  
> HTTP автоматически перенаправляется на HTTPS.  
> `ws://` не поддерживается.

| Компонент      | URL                               | Протокол |
|---------------|------------------------------------|----------|
| Админ-панель  | https://localhost:8083/admin       | HTTPS    |
| WebSocket     | wss://localhost:8083/ws            | WSS      |

## 📋 WebSocket API

### 1️⃣  Получение списка комнат
**📥 Запрос**
```json
{
  "type": "list_rooms",
  "userId": 1
}
```

**📤 Ответ**
```json
{
  "type": "list_rooms",
  "rooms": [
    {"id": 1, "name": "test"},
    {"id": 2, "name": "test1"}
  ]
}
```

### 2️⃣  Вход в комнату
**📥 Запрос**
```json
{
  "type": "join_room",
  "userId": 1,
  "roomId": 1,
  "minutes": 30
}
```

**📤 Ответ**
```json
{
  "type": "room_history",
  "roomId": 1,
  "roomName": "test",
  "messages": [
    {
      "id": 1,
      "content": "ttt!",
      "senderId": 1,
      "senderName": "User 1",
      "createdAt": "11111"
    }
  ]
}
```

### 3️⃣  Выход из комнаты
**📥 Запрос**
```json
{
  "type": "leave_room",
  "userId": 1,
  "roomId": 1
}
```

**📡 Broadcast**
```json
{
  "type": "leave_room",
  "roomId": 1,
  "userId": 1
}
```

### 4️⃣  Отправка сообщения
**📥 Запрос**
```json
{
  "type": "send_message",
  "userId": 1,
  "roomId": 1,
  "message": "Test!"
}
```

**📡 Broadcast**
```json
{
  "type": "send_message",
  "roomId": 1,
  "content": "Test!",
  "senderId": 1,
  "senderName": "User 1"
}
```

## 🔧 Разработка
### Запуск cs-fixer

```bash
vendor/bin/php-cs-fixer fix
```
Проверка без изменения файлов:
```bash
vendor/bin/php-cs-fixer fix --dry-run --diff
```

### Статический анализ
```bash
vendor/bin/phpstan analyse --configuration=phpstan.neon
```

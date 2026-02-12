ws://localhost:8080`

### список комнат
```json
{
  "type": "list_rooms",
  "userId": 1
}
```

**Ответ:**
```json
{
  "type": "rooms_list",
  "rooms": [
    {"id": 1, "name": "test"},
    {"id": 2, "name": "test1"}
  ]
}
```

### Войти в комнату
```json
{
  "type": "join_room",
  "userId": 1,
  "roomId": 1,
  "minutes": 30
}
```

**Ответ:**
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

### Выйти из комнаты
```json
{
  "type": "leave_room",
  "userId": 1,
  "roomId": 1
}
```

**участникам комнаты прийдет:**
```json
{
  "type": "user_left",
  "roomId": 1,
  "userId": 1
}
```

### Отправить сообщение
```json
{
  "type": "send_message",
  "userId": 1,
  "roomId": 1,
  "message": "Test!"
}
```

**участникам комнаты прийдет:**
```json
{
  "type": "new_message",
  "roomId": 1,
  "content": "Test!",
  "senderId": 1,
  "senderName": "User 1"
}
```

## Запуск phpstan и cs-fixer

```bash
# cs-fixer
vendor/bin/php-cs-fixer fix
vendor/bin/php-cs-fixer fix --dry-run --diff

# phpstan
vendor/bin/phpstan analyse --configuration=phpstan.neon
```

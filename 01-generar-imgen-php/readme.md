### Generador de Imágenes de Avatar API

#### Endpoint:
- **URL:** `http://test.test/avatar.php`

#### Parámetros:
- `name`: Nombre para el avatar.
- `size`: Tamaño del avatar (en píxeles).
- `background`: Color de fondo del avatar (`random` para aleatorio).
- `color`: Color del texto en el avatar (formato hexadecimal).
- `rounded`: Booleano (`true` o `false`) para especificar si el avatar debe ser redondeado.

#### Ejemplo de Uso:
```bash
avatar.php?name=Guido%20Laime&size=200&background=random&color=ffffff&rounded=true
```

#### Descripción:
Este endpoint genera imágenes de avatar personalizadas según los parámetros especificados en la URL. Acepta nombres, tamaños, colores de fondo y texto, y opciones para redondear el avatar.

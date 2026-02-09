# 🛡️ CyberEdu - Solución de Problemas de Login en Servidor

## ❌ Problemas Identificados y Solucionados

### Problema 1: logout.php redirigía a login.html
**Causado por:** Redirección a un archivo HTML en lugar de PHP
**Solucionado:** ✅ Ahora redirige a `login.php`

### Problema 2: Rutas relativas inconsistentes
**Causado por:** Si el sitio se sube a un subdirectorio (ej: `/proyecto/PI-PCMB/`), las rutas pueden fallar
**Solucionado:** ✅ Creado `config.php` que detecta automáticamente la ruta base

### Problema 3: Falta de .htaccess
**Causado por:** Sin reglas de reescritura, los archivos `.html` no redirigen a `.php`
**Solucionado:** ✅ Creado `.htaccess` con reglas de reescritura

---

## ✅ Cambios Realizados

### 1. **config.php** (NUEVO)
- Detecta automáticamente el protocolo (http/https)
- Detecta si está en un subdirectorio
- Proporciona funciones `redirect()` y `getUrl()`
- Centraliza configuración de sesiones

### 2. **login.php**
- Ahora incluye `config.php`
- Usa `redirect()` en lugar de `header('Location:')`
- Mejor manejo de errores

### 3. **logout.php**
- Redirige a `login.php` (CORREGIDO de login.html)
- Usa configuración centralizada

### 4. **index.php**
- Usa `redirect()`
- Incluye `config.php`

### 5. **panel.php**
- Usa `redirect()`
- Incluye `config.php`

### 6. **.htaccess** (NUEVO)
- Redirige `login.html` → `login.php`
- Redirige `register.html` → `register.php`
- Previene acceso directo a `users.json`
- Mejora seguridad

### 7. **diagnostico.php** (NUEVO)
- Script para verificar que todo está configurado correctamente
- Comprueba: PHP version, sesiones, bcrypt, permisos de archivos, etc.

---

## 📋 Requisitos en el Servidor

### ✅ OBLIGATORIO
- [ ] **PHP >= 7.0**
- [ ] **Módulo `session`** habilitado
- [ ] **Función `password_verify()`** disponible (para bcrypt)
- [ ] **Soporte para `header()` redirects**
- [ ] **Archivo `users.json` legible**
- [ ] **Directorio `logs/` con permisos de escritura** (se crea automáticamente)

### 📌 RECOMENDADO
- [ ] **Apache con módulo `mod_rewrite`** (para .htaccess)
- [ ] **HTTPS habilitado** (seguridad)
- [ ] **PHP con soporte para opciones `session.cookie_secure`**

---

## 🚀 Pasos de Instalación en el Servidor

### 1. Subir archivos
```bash
rsync -avz ./ usuario@servidor:/home/usuario/public_html/PI-PCMB/
```

### 2. Verificar permisos
```bash
chmod 755 /home/usuario/public_html/PI-PCMB/logs
chmod 644 /home/usuario/public_html/PI-PCMB/users.json
chmod 644 /home/usuario/public_html/PI-PCMB/.htaccess
chmod 644 /home/usuario/public_html/PI-PCMB/config.php
```

### 3. Verificar configuración
Acceder a: `https://projecte.pcolmenarejo.com/diagnostico.php`

Debería mostrar "✓" para:
- [ ] PHP Version
- [ ] Soporte de Sesiones
- [ ] Directorio de Sesiones (escribible)
- [ ] Soporte Bcrypt
- [ ] Archivo users.json
- [ ] Test de Sesión

### 4. Probar login
1. Acceder a `https://projecte.pcolmenarejo.com/`
   - Debería redirigir a `/login.php` si no está autenticado
2. Ingresar credenciales:
   - Usuario: `admin`
   - Contraseña: `123456`
3. Debería redirigir a `/panel.php`
4. Hacer logout debería redirigir a `/login.php`

---

## 🔍 Solución de Problemas Específicos

### ❌ "Redirección infinita en login.php"
**Causa:** Las sesiones no se están guardando
**Solución:** 
1. Ejecutar `diagnostico.php`
2. Verificar que "Test de Sesión" muestre "✓"
3. Si no:
   - Contactar al soporte del hosting
   - Verificar que el directorio session_save_path existe

### ❌ "Login no funciona pero llega a panel.php sin datos"
**Causa:** Las sesiones se crean pero no persisten
**Solución:**
1. Verificar que `$_SESSION['user_id']` se está asignando
2. Revisar logs de PHP: `/var/log/php-errors.log`
3. Asegurar que `ini_set('session.use_only_cookies', 1)` está en config.php

### ❌ "La redirección a panel.php no funciona"
**Causa:** Headers ya fueron enviados antes de `header()`
**Solución:**
1. Verificar que NO hay espacios en blanco antes de `<?php`
2. Revisar que `config.php` se incluye PRIMERO
3. No usar `echo`, `print`, o salida HTML antes de `redirect()`

### ❌ ".htaccess no redirige login.html a login.php"
**Causa:** `mod_rewrite` no está habilitado
**Solución:**
1. Contactar soporte del hosting y pedir habilitar `mod_rewrite`
2. O renombrar `login.html` a algo como `login-home.html`

### ❌ "users.json no se encuentra"
**Causa:** Ruta incorrecta o archivo no subido
**Solución:**
1. Verificar que `users.json` está en la raíz del proyecto
2. Ejecutar `diagnostico.php` para confirmar

---

## 📊 Nuevo Flujo de Autenticación (v2.0)

```
┌─────────────────────────────────┐
│ Usuario accede a /              │
│ (con ruta base automática)      │
└────────────┬────────────────────┘
             │
      ┌──────┴──────┐
      │             │
  ✅ Sesión    ❌ No hay sesión
      │             │
      ▼             ▼
 index.php      config.php
 (si hay        detecta ruta
 user_id)            │
      │              ▼
      │           login.php
      │          (formulario)
      │              │
      │          credenciales
      │              │
      │              ▼
      │          ✅ Correctas
      │              │
      │              ▼
      │          redirect()
      │          (usa config.php)
      │              │
      └──────┬───────┘
             │
             ▼
         panel.php
        (panel control)
```

---

## 🔐 Credenciales de Prueba

```
Usuario: admin
Contraseña: 123456
Hash: $2y$10$7YJjV3BOFXL/L65zzVV4EuPHSKCMO9eSzk61B8mqKQFEu6rn9ytwm

Usuario: usuario
Contraseña: 123456
Hash: $2y$10$7YJjV3BOFXL/L65zzVV4EuPHSKCMO9eSzk61B8mqKQFEu6rn9ytwm
```

---

## 📝 Archivos Modificados/Creados

### ✅ CREADOS
- `config.php` - Configuración global y detección automática de rutas
- `.htaccess` - Reglas de reescritura y seguridad
- `diagnostico.php` - Script para verificar configuración del servidor

### ✅ MODIFICADOS
- `login.php` - Ahora usa `config.php`
- `logout.php` - Redirige a `login.php` (corregido)
- `index.php` - Usa `config.php` y `redirect()`
- `panel.php` - Usa `config.php` y `redirect()`

---

## 🎯 Próximo Paso

**Ejecutar `diagnostico.php` en el servidor para verificar que todo está bien configurado.**

Si aún hay problemas, ejecutar:
```bash
grep -r "Location:" *.php | grep -v "config.php"
```

Para asegurarse que todas las redirecciones usan las rutas correctas.

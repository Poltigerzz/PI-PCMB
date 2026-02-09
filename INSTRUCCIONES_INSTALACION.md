# 🚀 INSTRUCC IONES DE INSTALACIÓN - Login v2.0

## 📦 ARCHIVOS NUEVOS (DEBES SUBIR AL SERVIDOR)

```
✅ NUEVO config.php                    - Configuración centralizada
✅ NUEVO .htaccess                     - Reglas Apache
✅ NUEVO diagnostico.php               - Herramienta de diagnóstico
✅ NUEVO test-login.html               - Test de login
✅ NUEVO verificar-login.sh            - Script de verificación
✅ NUEVO CAMBIOS_v2.0.md               - Documentación de cambios
✅ NUEVO SOLUCION_LOGIN_v2.md          - Guía completa
✅ NUEVO RESUMEN_SOLUCION.md           - Resumen ejecutivo
```

## 📝 ARCHIVOS MODIFICADOS (DEBES ACTUALIZAR EN EL SERVIDOR)

```
✏️  MODIFICADO logout.php              - CRÍTICO: Ahora redirige a login.php
✏️  MODIFICADO login.php               - Usa config.php
✏️  MODIFICADO index.php               - Usa config.php
✏️  MODIFICADO panel.php               - Usa config.php
```

## 📋 ARCHIVOS SIN CAMBIOS (NO NECESITA SUBIR)

```
⚪ SIN CAMBIOS index.html
⚪ SIN CAMBIOS login.html
⚪ SIN CAMBIOS login.css
⚪ SIN CAMBIOS styles.css
⚪ SIN CAMBIOS test.html
⚪ SIN CAMBIOS users.json
⚪ SIN CAMBIOS README.md
⚪ SIN CAMBIOS FIXES_APPLIED.md
```

---

## 🔧 PASOS DE INSTALACIÓN

### PASO 1: Preparar archivos
```bash
# En tu carpeta local /workspaces/PI-PCMB, verifica que tienes:
ls -la | grep -E "config.php|.htaccess|diagnostico.php"

# Debería mostrar:
# -rw-r--r-- config.php
# -rw-r--r-- .htaccess
# -rw-r--r-- diagnostico.php
```

### PASO 2: Subir archivos al servidor
```bash
# Opción A: Con rsync (RECOMENDADO)
rsync -avz --delete ./ usuario@servidor:/path/to/public_html/PI-PCMB/

# Opción B: Con scp
scp config.php logout.php login.php index.php panel.php .htaccess diagnostico.php usuario@servidor:/path/to/public_html/PI-PCMB/
```

### PASO 3: Establecer permisos en el servidor
```bash
# SSH en el servidor
ssh usuario@servidor

# Ir a directorio
cd /path/to/public_html/PI-PCMB

# Permisos de directorio de logs
chmod 755 logs

# Permisos de archivos de configuración
chmod 644 config.php
chmod 644 .htaccess
chmod 644 diagnostico.php
chmod 644 logout.php
chmod 644 login.php
chmod 644 index.php
chmod 644 panel.php
```

### PASO 4: Verificar instalación
```bash
# Opción A: Acceder al diagnóstico en el navegador
https://projecte.pcolmenarejo.com/diagnostico.php

# Debería mostrar ✓ en todos los tests

# Opción B: Ejecutar script (en el servidor)
bash verificar-login.sh
```

---

## 🧪 PRUEBA DE FUNCIONALIDAD

### Test 1: Diagnóstico
1. Acceder a: `https://projecte.pcolmenarejo.com/diagnostico.php`
2. Verificar que muestra:
   - ✓ PHP Version
   - ✓ Soporte de Sesiones
   - ✓ Soporte Bcrypt
   - ✓ Archivo users.json
   - ✓ Test de Sesión

### Test 2: Interfaz de Test
1. Acceder a: `https://projecte.pcolmenarejo.com/test-login.html`
2. Hacer clic en "Probar Login" con credenciales:
   - Usuario: `admin`
   - Contraseña: `123456`
3. Debería redirigir a `/panel.php`

### Test 3: Flujo Completo
1. **Acceder sin autenticación:**
   ```
   https://projecte.pcolmenarejo.com/
   → Debería redirigir a /login.php
   ```

2. **Hacer login:**
   ```
   Usuario: admin
   Contraseña: 123456
   → Debería redirigir a /panel.php
   ```

3. **Hacer logout:**
   ```
   Clic en "Tancar Sessió"
   → Debería redirigir a /login.php
   ```

### Test 4: Acceso Directo a panel.php sin autenticación
```
Acceder a: https://projecte.pcolmenarejo.com/panel.php (sin login)
→ Debería redirigir a /login.php
```

---

## ⚠️ TROUBLESHOOTING

### Problema: Redirección infinita
**Síntoma:** Página se recarga constantemente
**Solución:**
1. Ejecutar `diagnostico.php`
2. Revisa: "Test de Sesión" - ¿muestra "✓"?
3. Si no: Contactar soporte del hosting

### Problema: Login funciona pero no va a panel.php
**Síntoma:** Se queda en login.php aunque dice "Login exitoso"
**Solución:**
1. Verificar que `config.php` está bien subido
2. Revisar logs: `tail -f /var/log/php-errors.log`
3. Asegurar que no hay espacios en blanco antes de `<?php`

### Problema: .htaccess genera error 500
**Síntoma:** Error 500 cuando accedes al sitio
**Solución:**
1. Renombrar `.htaccess` a `.htaccess.bak` temporalmente
2. Pruebar acceso directo: `https://projecte.pcolmenarejo.com/login.php`
3. Si funciona, contactar soporte para ver por qué .htaccess falla
4. Posible causa: `mod_rewrite` no habilitado

### Problema: users.json no se encuentra
**Síntoma:** "usuario no encontrado" aunque credenciales son correctas
**Solución:**
1. Verificar que `users.json` está en la raíz
2. Ejecutar: `ls -la users.json` - debe mostrar el archivo
3. Asegurar que tiene permisos de lectura: `chmod 644 users.json`

---

## 📊 CAMBIOS CLAVE

| Archivo | Cambio Clave |
|---------|--------------|
| `logout.php` | `:q header('Location: login.html')` → `redirect('login.php')` |
| `login.php` | Incluye `config.php` en la primera línea |
| `index.php` | Incluye `config.php` en la primera línea |
| `panel.php` | Incluye `config.php` en la primera línea |
| `config.php` | NUEVO - Detecta automáticamente rutas |
| `.htaccess` | NUEVO - Reglas de reescritura y seguridad |

---

## ✅ CHECKLIST FINAL

Antes de dar por completa la instalación, verifica:

- [ ] Todos los archivos NUEVOS están en el servidor
- [ ] Todos los archivos MODIFICADOS están actualizados
- [ ] Permisos están correctos (755 para directorios, 644 para archivos)
- [ ] `diagnostico.php` muestra todos los tests en ✓
- [ ] Puedes acceder a `/login.php` directamente
- [ ] El login funciona con admin/123456
- [ ] Te redirige a `/panel.php` después del login
- [ ] El logout funciona y te redirige a `/login.php`
- [ ] Acceder a `/` sin autenticación redirige a `/login.php`

---

## 🆘 SOPORTE

Si aún tienes problemas después de estos pasos:

1. **Recolecta información:**
   ```bash
   # En el servidor, ejecutar:
   php diagnostico.php > diagnostico-resultado.txt
   tail -20 /var/log/php-errors.log > errores-php.txt
   curl -I https://projecte.pcolmenarejo.com/config.php
   ```

2. **Contacta soporte del hosting con:**
   - ¿Tienes `mod_rewrite` habilitado?
   - ¿Cuál es `session_save_path`?
   - ¿Pueden revisar los logs de PHP?
   - ¿Cuál es la versión de PHP?

3. **Si usar Git:**
   ```bash
   git status  # Ver qué cambió
   git diff    # Ver diferencias exactas
   ```

---

## 🎯 RESUMEN

**Los 3 cambios fundamentales:**
1. ✅ `logout.php` ahora redirige a `login.php` (CRÍTICO)
2. ✅ Se crea `config.php` para detectar rutas automáticamente
3. ✅ Se actualiza `login.php`, `index.php`, `panel.php` para usar `config.php`

**Resultado:** El sistema funciona en cualquier servidor, sea en raíz o en subdirectorio.

---

**¡Listo! La instalación debería estar completa. Ejecuta `diagnostico.php` para verificar.**

#!/bin/bash
# Script de verificación para el servidor
# Ejecutar en el servidor con: bash verificar-login.sh

echo "================================"
echo "🔍 Verificación de Login - CyberEdu"
echo "================================"
echo ""

# Verificar que PHP está disponible
echo "1. Verificando PHP..."
if command -v php &> /dev/null; then
    php -v | head -n 1
else
    echo "❌ PHP no está disponible"
fi
echo ""

# Verificar que los archivos están presentes
echo "2. Verificando archivos..."
FILES=("config.php" "login.php" "logout.php" "index.php" "panel.php" ".htaccess" "users.json" "diagnostico.php")

for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ $file"
    else
        echo "❌ $file - NO ENCONTRADO"
    fi
done
echo ""

# Verificar permisos
echo "3. Verificando permisos..."
if [ -d "logs" ]; then
    if [ -w "logs" ]; then
        echo "✅ logs/ - Directorio escribible"
    else
        echo "❌ logs/ - No tiene permisos de escritura"
    fi
else
    echo "⚠️  logs/ - Directorio no existe (se creará automáticamente)"
fi

if [ -r "users.json" ]; then
    echo "✅ users.json - Legible"
else
    echo "❌ users.json - No es legible"
fi
echo ""

# Verificar módulos PHP
echo "4. Verificando módulos PHP..."
php -m 2>/dev/null | grep -q "json" && echo "✅ JSON módulo" || echo "❌ JSON módulo"
php -m 2>/dev/null | grep -q "session" && echo "✅ SESSION módulo" || echo "❌ SESSION módulo"
echo ""

# Probar creación de sesión
echo "5. Probando sesión con PHP..."
php -r "
session_start();
\$_SESSION['test'] = 'working';
echo \$_SESSION['test'] === 'working' ? '✅ Sesiones funcionan' : '❌ Error de sesiones';
" 2>/dev/null
echo ""

# Probar bcrypt
echo "6. Probando bcrypt..."
php -r "
\$hash = '\$2y\$10\$7YJjV3BOFXL/L65zzVV4EuPHSKCMO9eSzk61B8mqKQFEu6rn9ytwm';
\$result = password_verify('123456', \$hash) ? 'verificada' : 'no verificada';
echo '✅ Hash de prueba: ' . \$result;
" 2>/dev/null
echo ""

echo "================================"
echo "✅ Verificación completada"
echo "================================"
echo ""
echo "Próximo paso:"
echo "  Acceder a: https://projecte.pcolmenarejo.com/diagnostico.php"

#!/usr/bin/env bash
# =============================================================
# Colección de pruebas con curl – Autenticación por Token
# Práctica 2 – REST API con autenticación Bearer
#
# USO:
#   chmod +x tests.sh
#   ./tests.sh
#
# Cambia BASE_URL si tu servidor tiene otra dirección o puerto.
# =============================================================

BASE_URL="http://localhost/api/v1"

# Colores para output legible
GREEN='\033[0;32m'
RED='\033[0;31m'
CYAN='\033[0;36m'
YELLOW='\033[1;33m'
NC='\033[0m' # Sin color

separator() {
    echo -e "\n${CYAN}-------------------------------------------------------${NC}"
    echo -e "${CYAN}  $1${NC}"
    echo -e "${CYAN}-------------------------------------------------------${NC}"
}

# =============================================================
# 1. Login exitoso
# =============================================================
separator "1. Login exitoso (POST /login)"
echo -e "${YELLOW}Esperado: 200 OK con access_token${NC}"
LOGIN_RESPONSE=$(curl -s -w "\n%{http_code}" -X POST "$BASE_URL/login" \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"Admin1234!"}')

HTTP_CODE=$(echo "$LOGIN_RESPONSE" | tail -n1)
BODY=$(echo "$LOGIN_RESPONSE" | head -n -1)

echo "HTTP $HTTP_CODE"
echo "$BODY" | python3 -m json.tool 2>/dev/null || echo "$BODY"

# Extraer el token para los siguientes tests
TOKEN=$(echo "$BODY" | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('access_token',''))" 2>/dev/null)
echo -e "\n${GREEN}Token extraído: ${TOKEN:0:20}...${NC}"

# =============================================================
# 2. Login fallido - credenciales incorrectas
# =============================================================
separator "2. Login fallido - contrasena incorrecta (POST /login)"
echo -e "${YELLOW}Esperado: 401 Unauthorized${NC}"
curl -s -w "\nHTTP %{http_code}\n" -X POST "$BASE_URL/login" \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"Incorrecta123"}'

# =============================================================
# 3. Login fallido - usuario inexistente
# =============================================================
separator "3. Login fallido - usuario inexistente (POST /login)"
echo -e "${YELLOW}Esperado: 401 Unauthorized (mensaje generico)${NC}"
curl -s -w "\nHTTP %{http_code}\n" -X POST "$BASE_URL/login" \
  -H "Content-Type: application/json" \
  -d '{"username":"no_existe","password":"cualquier"}'

# =============================================================
# 4. Login fallido - usuario INACTIVO
# =============================================================
separator "4. Login con usuario INACTIVO (POST /login)"
echo -e "${YELLOW}Esperado: 401 Unauthorized${NC}"
curl -s -w "\nHTTP %{http_code}\n" -X POST "$BASE_URL/login" \
  -H "Content-Type: application/json" \
  -d '{"username":"inactivo","password":"Admin1234!"}'

# =============================================================
# 5. Perfil del usuario autenticado - GET /me
# =============================================================
separator "5. Perfil autenticado (GET /me)"
echo -e "${YELLOW}Esperado: 200 OK con datos del usuario${NC}"
curl -s -w "\nHTTP %{http_code}\n" -X GET "$BASE_URL/me" \
  -H "Authorization: Bearer $TOKEN"

# =============================================================
# 6. Recurso protegido CON token valido
# =============================================================
separator "6. Acceso CON token valido (GET /productos)"
echo -e "${YELLOW}Esperado: 200 OK con lista de productos${NC}"
curl -s -w "\nHTTP %{http_code}\n" -X GET "$BASE_URL/productos" \
  -H "Authorization: Bearer $TOKEN"

# =============================================================
# 7. Recurso protegido SIN token
# =============================================================
separator "7. Acceso SIN token (GET /productos)"
echo -e "${YELLOW}Esperado: 401 Unauthorized${NC}"
curl -s -w "\nHTTP %{http_code}\n" -X GET "$BASE_URL/productos"

# =============================================================
# 8. Recurso protegido CON token invalido
# =============================================================
separator "8. Acceso con token INVALIDO (GET /productos)"
echo -e "${YELLOW}Esperado: 401 Unauthorized${NC}"
curl -s -w "\nHTTP %{http_code}\n" -X GET "$BASE_URL/productos" \
  -H "Authorization: Bearer token_inventado_que_no_existe"

# =============================================================
# 9. Acceso a usuarios CON token valido
# =============================================================
separator "9. Lista de usuarios CON token (GET /users)"
echo -e "${YELLOW}Esperado: 200 OK${NC}"
curl -s -w "\nHTTP %{http_code}\n" -X GET "$BASE_URL/users" \
  -H "Authorization: Bearer $TOKEN"

# =============================================================
# 10. Logout - revocar token
# =============================================================
separator "10. Logout - revocar token (POST /logout)"
echo -e "${YELLOW}Esperado: 200 OK, token revocado${NC}"
curl -s -w "\nHTTP %{http_code}\n" -X POST "$BASE_URL/logout" \
  -H "Authorization: Bearer $TOKEN"

# =============================================================
# 11. Usar el token DESPUES del logout
# =============================================================
separator "11. Usar token revocado tras logout (GET /me)"
echo -e "${YELLOW}Esperado: 401 Unauthorized (token revocado)${NC}"
curl -s -w "\nHTTP %{http_code}\n" -X GET "$BASE_URL/me" \
  -H "Authorization: Bearer $TOKEN"

echo -e "\n${GREEN}Coleccion de pruebas completada.${NC}\n"

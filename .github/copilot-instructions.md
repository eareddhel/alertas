## copilot-instructions.md

## estilo de código

- **Nomenclatura:** Usa camelCase para variables y funciones, PascalCase para clases.
- **Tabulación:** Usa 2 espacios por tabulación, no uses tabuladores.
- **Comentarios:** Siempre en minúsculas, con un tono claro y conciso.
- **PHP:** Usa PHP 8.2.12. Prioriza POST y sesiones ($_SESSION). Solo usa GET si es estrictamente necesario.
- **Frontend:** Usa Bootstrap 5.3.3. Los estilos CSS deben ir en archivos externos dentro de `css/`.
- **Privacidad:** Política de Privacidad Estricta (nunca sugerir logs de datos sensibles).
- **Seguridad:** Siempre sugiere prácticas de seguridad, como validación de entradas y protección contra CSRF.
- **URL local** Todos los dominios virtuales tienen el formato 'https://nombre-proyecto.pwa' la extensión .pwa es el dominio por defecto para desarrollo local.
  
  Usar:

  ## S E G U R I D A D ##
Header add Access-Control-Allow-Origin "https://classbook.cl"

<IfModule mod_headers.c>
  Header set Content-Security-Policy " \
    upgrade-insecure-requests; \
    base-uri 'self'; \
    manifest-src 'self'; \
    worker-src 'self'; \
    object-src 'none'; \
    frame-src 'self'; \
    frame-ancestors 'none'; \
    form-action 'self'; \
    media-src 'self'; \
    img-src 'self' data:; \
    font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net; \
    style-src 'self' 'sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3'; \
    script-src 'self' 'sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p' 'unsafe-inline'; \
    style-src-elem 'self' https: 'unsafe-inline'; \
    script-src-elem 'self' https: 'unsafe-inline'; \
  "
</IfModule>
Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
Header set X-XSS-Protection "1; mode=block"

<Files .htaccess>
Order allow,deny
Deny from all
</Files>

<Files "configuration.php">
order allow,deny
deny from all
</Files>

Options All -Indexes
Options -Indexes
IndexIgnore *

<FilesMatch "\.(htaccess|htpasswd|ini|phps|fla|psd|log|sh)$">
  Order Allow,Deny
  Deny from all
</FilesMatch>

- **Estructura de Archivos:** Sigue la estructura estándar de MVC para organizar el código.
- **Nomenclatura:** Usa camelCase para variables y funciones, PascalCase para clases.
- **Errores:** Siempre maneja errores de manera adecuada, sugiriendo try-catch donde sea necesario.
- **Documentación:** Sugiere comentarios claros y concisos para funciones y clases, siguiendo el formato PHPDoc.
- **Testing:** Sugiere escribir pruebas unitarias para funciones críticas usando PHPUnit.
- **Version Control:** Siempre sugiere usar Git para el control de versiones, con mensajes de commit claros y descriptivos.
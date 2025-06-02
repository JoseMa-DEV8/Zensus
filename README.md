<p align="center">
<img src="https://i.imgur.com/pE6RhI1.png" alt="ReserV logo">
</p

**Zensus** es una plataforma web desarrollada en Laravel que automatiza la recolección, estructuración y visualización de datos públicos del Instituto Nacional de Estadística (INE). Utiliza scraping y APIs oficiales para obtener estadísticas demográficas, económicas y laborales a nivel provincial y municipal.

---

## 📊 Funcionalidades principales

- ✅ Importación de datos vía **API REST** y **web scraping**
- ✅ Almacenamiento relacional con **MySQL**
- ✅ Visualización dinámica con **Chart.js**
- ✅ Gráficos: evolución de población, pirámide de edad, renta media, desempleo, estructura empresarial...
- ✅ Filtros interactivos por provincia, municipio y año
- ✅ Sistema modular para añadir nuevas fuentes del INE fácilmente

---

## ⚙️ Tecnologías utilizadas

| Tipo           | Tecnología         |
|----------------|--------------------|
| Backend        | Laravel, PHP       |
| Frontend       | Blade, Chart.js    |
| Base de datos  | MySQL              |
| Scraping       | Guzzle, Puppeteer (según caso) |
| Otros          | Composer, Git, Vite, Artisan  |

---

## 🚀 Instalación rápida

```bash
# Clona el proyecto
git clone https://github.com/JoseMa-DEV8/Zensus.git
cd Zensus

# Instala dependencias de PHP
composer install

# Instala dependencias de Node.js
npm install && npm run build

# Copia y configura el archivo .env
cp .env.example .env
php artisan key:generate

# Configura tu base de datos en .env y ejecuta migraciones
php artisan migrate

# (Opcional) Importa datos del INE
php artisan ine:importar-datos


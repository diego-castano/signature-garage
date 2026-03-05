<?php
/**
 * Template Name: Importación de Clásicos
 *
 * @package signaturecar
 */

// Add SEO Meta Tags and Animation Library
add_action('wp_head', function () {
    ?>
    <meta name="description"
        content="Importa tu auto clásico de colección con Signature Garage. Servicio llave en mano desde la búsqueda hasta la entrega en Uruguay. Vehículos de 50+ años con exoneración.">
    <meta property="og:title" content="Importación de Clásicos | Signature Garage">
    <meta property="og:description"
        content="Tu sueño de coleccionista hecho realidad. Importamos clásicos de 50+ años con gestión completa.">
    <meta property="og:image"
        content="https://signature-garage.com/wp-content/uploads/2024/05/DSC02836-Enhanced-NR-Edit-1.jpg">
    <meta property="og:type" content="website">

    <!-- Fonts & Animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;700&display=swap" rel="stylesheet">
    <?php
}, 1);

get_header(); ?>

<style>
    /* Hide Default Newsletter on this specific page */
    .newsletter-area {
        display: none !important;
    }

    /* Custom Styles for Landing Page - Impactful Overhaul */
    .landing-hero {
        background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://signature-garage.com/wp-content/uploads/2024/05/DSC02836-Enhanced-NR-Edit-1.jpg') no-repeat center center;
        background-size: cover;
        height: 80vh;
        min-height: 600px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #fff;
        margin-top: 0;
        padding-top: 0;
        position: relative;
    }

    .hero-content-inner {
        max-width: 1100px;
        padding: 0 20px;
    }

    .landing-hero h1 {
        font-family: 'Oswald', sans-serif;
        font-size: 3.2rem;
        font-weight: 700;
        line-height: 1.2;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 20px;
        color: #fff;
    }

    .hero-subtitle {
        font-family: 'Oswald', sans-serif;
        font-size: 1.2rem;
        text-transform: uppercase;
        letter-spacing: 15px;
        color: #AC1D28;
        font-weight: 400;
        margin-bottom: 20px;
        display: block;
    }

    .hero-brand {
        font-family: 'Oswald', sans-serif;
        font-size: 1.8rem;
        letter-spacing: 12px;
        font-weight: 300;
        text-transform: uppercase;
        margin-top: 30px;
        color: rgba(255, 255, 255, 0.8);
    }

    .section-padding {
        padding: 120px 0;
    }

    .bg-dark-gray {
        background-color: #303137;
        color: #fff;
    }

    .text-red {
        color: #AC1D28 !important;
    }

    /* Titles with Oswald */
    .intro-title {
        font-family: 'Oswald', sans-serif;
        font-size: 2.4rem;
        font-weight: 700;
        margin-bottom: 40px;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .intro-text {
        font-family: 'Gill Sans', 'Gill Sans MT', Calibri, sans-serif;
        font-size: 1.4rem;
        line-height: 1.9;
        max-width: 900px;
        margin: 0 auto 30px auto;
        color: #f5f5f5;
        font-weight: 300;
    }

    /* CAR GRID SYSTEM - 2 per line (50%) */
    .classic-gallery-wrapper {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 50px;
        max-width: 1240px;
        margin: 0 auto;
    }

    .car-card {
        background: #25262b;
        border-radius: 4px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
        height: 100%;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .car-card:hover {
        transform: translateY(-15px);
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.7);
        border-color: rgba(172, 29, 40, 0.4);
    }

    .car-image-container {
        height: 350px;
        /* Taller photos as requested */
        overflow: hidden;
        position: relative;
    }

    .car-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 1.2s ease;
        filter: contrast(1.05);
    }

    .car-card:hover img {
        transform: scale(1.1);
    }

    .car-card-body {
        padding: 40px;
    }

    .car-card h3 {
        font-family: 'Oswald', sans-serif;
        font-size: 1.8rem;
        margin-bottom: 12px;
        color: #fff;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .car-meta {
        font-size: 1rem;
        color: #AC1D28;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 20px;
        display: block;
        letter-spacing: 2px;
    }

    .car-card p {
        font-size: 1.1rem;
        color: #ccc;
        line-height: 1.7;
        font-weight: 300;
    }

    /* ACLARACION DESIGN */
    .aclaracion-box {
        margin: 100px auto 0 auto;
        max-width: 900px;
        background: #1a1b1e;
        border: 1px solid #AC1D28;
        padding: 40px;
        position: relative;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .aclaracion-badge {
        position: absolute;
        top: -15px;
        left: 50%;
        transform: translateX(-50%);
        background: #AC1D28;
        color: #fff;
        padding: 5px 25px;
        font-family: 'Oswald', sans-serif;
        text-transform: uppercase;
        letter-spacing: 3px;
        font-weight: 700;
        font-size: 0.9rem;
    }

    .aclaracion-box p {
        font-family: 'Gill Sans', sans-serif;
        font-size: 1.3rem;
        color: #fff;
        margin: 0;
        line-height: 1.7;
        font-style: italic;
    }

    /* Form Container Styling */
    .form-container {
        background: #fff;
        color: #1a1a1a;
        padding: 60px;
        border-radius: 4px;
        box-shadow: 0 40px 100px rgba(0, 0, 0, 0.6);
    }

    @media (max-width: 992px) {
        .classic-gallery-wrapper {
            grid-template-columns: 1fr;
            max-width: 600px;
        }

        .landing-hero h1 {
            font-size: 2.6rem;
        }

        .intro-title {
            font-size: 2rem;
        }
    }

    @media (max-width: 768px) {
        .landing-hero {
            height: auto;
            min-height: 550px;
            padding: 160px 20px 100px;
        }

        .landing-hero h1 {
            font-size: 2.2rem;
            letter-spacing: 1px;
        }

        .hero-brand {
            font-size: 1.2rem;
            letter-spacing: 6px;
        }

        .section-padding {
            padding: 80px 0;
        }

        .intro-title {
            font-size: 1.8rem;
        }

        .intro-text {
            font-size: 1.1rem;
        }

        .car-image-container {
            height: 280px;
        }
    }
</style>

<div id="primary" class="site-main">

    <!-- Hero Section -->
    <header class="landing-hero">
        <div class="hero-content-inner animate__animated animate__fadeIn">
            <span class="hero-subtitle">Tu próximo clásico</span>
            <h1>El link al auto de tus sueños</h1>
            <div class="hero-brand">Signature Garage</div>
        </div>
    </header>

    <!-- Explanatory Section -->
    <section class="section-padding bg-dark-gray">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-lg-10 animate__animated animate__fadeInUp">
                    <h2 class="intro-title text-white">Importación de Clásicos Llave en Mano</h2>
                    <p class="intro-text">
                        Signature Garage convierte su sueño de coleccionista en realidad. Usted ya no tiene que
                        preocuparse por la distancia, la búsqueda o la burocracia internacional. Su clásico ideal está
                        esperando en algún lugar del mundo, y nuestro servicio es traerlo hasta usted.
                    </p>
                    <p class="intro-text">
                        Con nuestra gestión llave en mano, nosotros nos encargamos absolutamente de todo: desde la
                        localización y la inspección exhaustiva del vehículo, hasta el cumplimiento de la normativa
                        uruguaya y la entrega final, empadronado y listo para disfrutar.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Examples Section -->
    <section class="section-padding" style="background-color: #1a1b1e;">
        <div class="container">
            <h2 class="intro-title text-uppercase text-center mb-5 text-white animate__animated animate__fadeIn">
                5 Ejemplos de Clásicos para Importar Hoy
                <span class="d-block mt-3 text-red"
                    style="font-size: 1.4rem; font-family: 'Oswald', sans-serif; letter-spacing: 5px; font-weight: 400;">(50+
                    años, exonerados)</span>
            </h2>

            <div class="classic-gallery-wrapper">

                <?php $theme_url = get_template_directory_uri(); ?>

                <!-- Mustang -->
                <div class="car-card animate__animated animate__fadeInUp">
                    <div class="car-image-container">
                        <img src="<?php echo $theme_url; ?>/autos/fordmustang.webp" alt="Ford Mustang Classic">
                    </div>
                    <div class="car-card-body">
                        <span class="car-meta">1964–1973 | 50+ Años: Sí</span>
                        <h3>Ford Mustang (1ra Gen.)</h3>
                        <p>El pony car americano por excelencia. Un ícono de la libertad y la velocidad que marcó una
                            era.</p>
                    </div>
                </div>

                <!-- Pagoda -->
                <div class="car-card animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                    <div class="car-image-container">
                        <img src="<?php echo $theme_url; ?>/autos/pagoda1963–1971.jpg"
                            alt="Mercedes-Benz Pagoda Classic">
                    </div>
                    <div class="car-card-body">
                        <span class="car-meta">1963–1971 | 50+ Años: Sí</span>
                        <h3>Mercedes-Benz Pagoda</h3>
                        <p>Elegancia atemporal. Un convertible de lujo con ingeniería alemana de precisión, perfecto
                            para Uruguay.</p>
                    </div>
                </div>

                <!-- Porsche 911 -->
                <div class="car-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                    <div class="car-image-container">
                        <img src="<?php echo $theme_url; ?>/autos/porscheclassic.jpg" alt="Porsche 911 Classic">
                    </div>
                    <div class="car-card-body">
                        <span class="car-meta">1964–1975 | 50+ Años: Sí</span>
                        <h3>Porsche 911 (Classic)</h3>
                        <p>La esencia del deportivo puro. Un diseño inconfundible que ha superado la prueba del tiempo.
                        </p>
                    </div>
                </div>

                <!-- Jaguar E-Type -->
                <div class="car-card animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                    <div class="car-image-container">
                        <img src="<?php echo $theme_url; ?>/autos/jaguar.webp" alt="Jaguar E-Type">
                    </div>
                    <div class="car-card-body">
                        <span class="car-meta">1961–1974 | 50+ Años: Sí</span>
                        <h3>Jaguar E-Type</h3>
                        <p>Considerado uno de los autos más bellos jamás creados. Pura distinción británica y desempeño.
                        </p>
                    </div>
                </div>

                <!-- Corvette C2 -->
                <div class="car-card animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
                    <div class="car-image-container">
                        <img src="<?php echo $theme_url; ?>/autos/corvette1963–1967.jpg" alt="Chevrolet Corvette C2">
                    </div>
                    <div class="car-card-body">
                        <span class="car-meta">1963–1967 | 50+ Años: Sí</span>
                        <h3>Corvette C2 (Sting Ray)</h3>
                        <p>El deportivo más audaz de los 60. Un diseño inolvidable con motor V8 puro que representa el
                            sueño americano.</p>
                    </div>
                </div>

            </div>

            <!-- ACLARACION BOX -->
            <div class="aclaracion-box animate__animated animate__fadeIn">
                <div class="aclaracion-badge">Aclaración</div>
                <p>La importación con exoneración aplica a vehículos de 50 años o más. Signature Garage te asesorará
                    sobre los requisitos de la Dirección Nacional de Aduanas y la prohibición de enajenación por un
                    período determinado.</p>
            </div>
        </div>
    </section>

    <!-- Form Section -->
    <section class="section-padding bg-dark-gray">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 animate__animated animate__zoomIn">
                    <div class="form-container">
                        <?php echo do_shortcode('[gravityform id="5" title="true"]'); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div><!-- #primary -->

<?php
get_footer();

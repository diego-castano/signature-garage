<?php if (!defined('ABSPATH')) exit; ?>

<div class="sgu-slider-manager">
    <!-- Header -->
    <div class="sgu-sm-header">
        <div>
            <h2>Hero Slider Manager</h2>
            <p class="sgu-sm-subtitle">Selecciona y ordena los vehiculos que aparecen en el slider del homepage.</p>
        </div>
        <div class="sgu-sm-actions">
            <span class="sgu-sm-status" id="sguSmStatus"></span>
            <button class="button button-primary sgu-sm-save" id="sguSmSave">Guardar Slider</button>
        </div>
    </div>

    <div class="sgu-sm-layout">
        <!-- Left: Vehicle Search -->
        <div class="sgu-sm-panel sgu-sm-search-panel">
            <div class="sgu-sm-panel-header">
                <h3>Vehiculos Disponibles</h3>
                <input type="text" id="sguSmSearch" class="sgu-sm-search-input" placeholder="Buscar por nombre, marca..." />
            </div>
            <div class="sgu-sm-results" id="sguSmResults">
                <p class="sgu-sm-hint">Busca un vehiculo o haz scroll para ver todos.</p>
            </div>
        </div>

        <!-- Right: Slider Items -->
        <div class="sgu-sm-panel sgu-sm-slider-panel">
            <div class="sgu-sm-panel-header">
                <h3>Slides del Hero <span class="sgu-sm-count" id="sguSmCount">0</span></h3>
            </div>
            <div class="sgu-sm-slides" id="sguSmSlides">
                <div class="sgu-sm-empty" id="sguSmEmpty">
                    <span class="dashicons dashicons-images-alt2"></span>
                    <p>Arrastra vehiculos aqui o usa el boton + para agregar.</p>
                </div>
            </div>

            <!-- Settings -->
            <div class="sgu-sm-settings">
                <h4>Configuracion del Slider</h4>
                <div class="sgu-sm-settings-grid">
                    <label class="sgu-sm-setting">
                        <span>Autoplay</span>
                        <input type="checkbox" id="sguSmAutoplay" checked />
                    </label>
                    <label class="sgu-sm-setting">
                        <span>Velocidad (ms)</span>
                        <input type="number" id="sguSmSpeed" value="4000" min="1000" max="15000" step="500" />
                    </label>
                    <label class="sgu-sm-setting">
                        <span>Transicion</span>
                        <select id="sguSmTransition">
                            <option value="fade">Fade</option>
                            <option value="slide">Slide</option>
                        </select>
                    </label>
                    <label class="sgu-sm-setting">
                        <span>Mostrar puntos</span>
                        <input type="checkbox" id="sguSmDots" checked />
                    </label>
                    <label class="sgu-sm-setting">
                        <span>Mostrar flechas</span>
                        <input type="checkbox" id="sguSmArrows" checked />
                    </label>
                    <label class="sgu-sm-setting">
                        <span>Mostrar info vehiculo</span>
                        <input type="checkbox" id="sguSmInfo" checked />
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

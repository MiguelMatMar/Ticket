<div class="main-content-wrapper">
    <div id="main-content">
        <h1 class="page-title">Detalles de la cuenta</h1>

        <div class="layout-container">
            <div class="content-left">
                <div class="custom-panel">
                    <div class="custom-panel-body">
                        <form action="/client/updateProfile" method="POST">
                            
                            <div class="section-header">
                                <h3>Información Personal</h3>
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group-custom">
                                    <label>Nombre</label>
                                    <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                                </div>
                                <div class="form-group-custom">
                                    <label>Apellido</label>
                                    <input type="text" name="apellidos" class="form-control" value="<?= htmlspecialchars($usuario['apellidos']) ?>" required>
                                </div>
                                <div class="form-group-custom">
                                    <label>Nombre de Compañía</label>
                                    <input type="text" name="empresa" class="form-control" value="<?= htmlspecialchars($usuario['empresa'] ?? '') ?>">
                                </div>
                                <div class="form-group-custom">
                                    <label>Dirección de E-Mail</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                                </div>
                                <div class="form-group-custom">
                                    <label>Número de Teléfono</label>
                                    <input type="tel" name="telefono" class="form-control" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">
                                </div>
                                <div class="form-group-custom">
                                    <label>NIF / CIF / NIE</label>
                                    <input type="text" name="nif" class="form-control" value="<?= htmlspecialchars($usuario['nif'] ?? '') ?>" placeholder="Ej: 12345678Z">
                                </div>
                            </div>

                            <div class="section-header">
                                <h3>Dirección de Facturación</h3>
                            </div>

                            <div class="form-grid">
                                <div class="form-group-custom span-2">
                                    <label>Dirección 1</label>
                                    <input type="text" name="direccion1" class="form-control" value="<?= htmlspecialchars($usuario['direccion1'] ?? '') ?>">
                                </div>
                                <div class="form-group-custom">
                                    <label>Dirección 2</label>
                                    <input type="text" name="direccion2" class="form-control" value="<?= htmlspecialchars($usuario['direccion2'] ?? '') ?>">
                                </div>
                                <div class="form-group-custom">
                                    <label>Ciudad</label>
                                    <input type="text" name="ciudad" class="form-control" value="<?= htmlspecialchars($usuario['ciudad'] ?? '') ?>">
                                </div>
                                <div class="form-group-custom">
                                    <label>Provincia/Región</label>
                                    <select name="provincia" class="form-control">
                                        <option value="">—</option>
                                        <?php 
                                        $provincias = ["ARABA/ÁLAVA", "ALBACETE", "ALICANTE", "ALMERIA", "AVILA", "BADAJOZ", "ILLES BALEARS", "BARCELONA", "BURGOS", "CACERES", "CADIZ", "CASTELLON", "CIUDAD REAL", "CORDOBA", "CORUÑA, A", "CUENCA", "GIRONA", "GRANADA", "GUADALAJARA", "GIPUZKOA", "HUELVA", "HUESCA", "JAEN", "LEON", "LLEIDA", "RIOJA, LA", "LUGO", "MADRID", "MALAGA", "MURCIA", "NAVARRA", "OURENSE", "ASTURIAS", "PALENCIA", "PALMAS, LAS", "PONTEVEDRA", "SALAMANCA", "SANTA CRUZ DE TENERIFE", "CANTABRIA", "SEGOVIA", "SEVILLA", "SORIA", "TARRAGONA", "TERUEL", "TOLEDO", "VALENCIA", "VALLADOLID", "BIZKAIA", "ZAMORA", "ZARAGOZA", "CEUTA", "MELILLA"];
                                        foreach($provincias as $prov): ?>
                                            <option value="<?= $prov ?>" <?= (isset($usuario['provincia']) && $usuario['provincia'] == $prov) ? 'selected' : '' ?>><?= $prov ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group-custom">
                                    <label>Código Postal</label>
                                    <input type="text" name="codigo_postal" class="form-control" value="<?= htmlspecialchars($usuario['codigo_postal'] ?? '') ?>">
                                </div>
                                <div class="form-group-custom">
                                    <label>País</label>
                                    <select name="pais" class="form-control">
                                        <?php 
                                        $paises = ["Afghanistan", "Aland Islands", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua And Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia And Herzegovina", "Botswana", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, Democratic Republic", "Cook Islands", "Costa Rica", "Cote D'Ivoire", "Croatia", "Cuba", "Curacao", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guernsey", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard Island & Mcdonald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran, Islamic Republic Of", "Iraq", "Ireland", "Isle Of Man", "Israel", "Italy", "Jamaica", "Japan", "Jersey", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea", "Kuwait", "Kyrgyzstan", "Lao People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macao", "Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States Of", "Moldova", "Monaco", "Mongolia", "Montenegro", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Palestine, State of", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Barthelemy", "Saint Helena", "Saint Kitts And Nevis", "Saint Lucia", "Saint Martin", "Saint Pierre And Miquelon", "Saint Vincent And Grenadines", "Samoa", "San Marino", "Sao Tome And Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia And Sandwich Isl.", "España", "Sri Lanka", "Sudan", "Suriname", "Svalbard And Jan Mayen", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor-Leste", "Togo", "Tokelau", "Tonga", "Trinidad And Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks And Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Viet Nam", "Virgin Islands, British", "Virgin Islands, U.S.", "Wallis And Futuna", "Western Sahara", "Yemen", "Zambia", "Zimbabwe"];
                                        foreach($paises as $p): ?>
                                            <option value="<?= $p ?>" <?= (isset($usuario['pais']) && $usuario['pais'] == $p) ? 'selected' : (($p == 'España' && empty($usuario['pais'])) ? 'selected' : '') ?>><?= $p ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group-custom">
                                    <label>Contacto de Facturación Predeterminado</label>
                                    <select name="contacto_facturacion" class="form-control">
                                        <option value="default">Contacto por defecto</option>
                                    </select>
                                </div>
                                <div class="form-group-custom">
                                    <label>Idioma</label>
                                    <select name="idioma" class="form-control">
                                        <option value="es" <?= (isset($usuario['idioma']) && $usuario['idioma'] == 'es') ? 'selected' : '' ?>>Español</option>
                                        <option value="en" <?= (isset($usuario['idioma']) && $usuario['idioma'] == 'en') ? 'selected' : '' ?>>English</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn-save">Guardar cambios</button>
                                <button type="reset" class="btn-cancel">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="sidebar-right">
                <div class="sidebar-panel">
                    <div class="sidebar-header">
                        <i class="fas fa-address-card"></i> Cuenta
                    </div>
                    <div class="sidebar-list">
                        <a href="/client/accdetails" class="sidebar-item active">Detalles de la cuenta</a>
                        <a href="/client/changepassword" class="sidebar-item">Cambiar Contraseña</a>
                        <?php if($usuario['rol'] === 'admin' || $usuario['rol'] === "soporte"): ?>
                            <a href="#" class="sidebar-item">Gestión de usuarios</a>
                            <a href="#" class="sidebar-item">Contactos</a> 
                        <?php endif; ?>
                        <a href="#" class="sidebar-item">Seguridad de la cuenta</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="citadela-layouts-progress" class="flex" <?php if ($type === 'download') { ?>style="display: none"<?php } ?>>
    <div class="m-auto">
        <?php if ($type === 'download') { ?>
        <div id="uploader" class="m-auto">
            <div class="upload-card flex">
                <div class="m-auto w-full">
                    <div id="uploader-progress" class="text-center">
                        <div><h3 class="text-gray-800"><?php _e( 'Downloading&#8230;', 'citadela-pro' ) ?></h3></div>
                        <div><progress value="100" max="100"></progress></div>
                    </div>
                    <div id="uploader-error" class="hidden">
                        <div><h3 id="uploader-error-title" class="text-gray-800"></h3></div>
                        <div id="uploader-error-msg" class="text-gray-700"></div>
                        <div class="text-center confirm-buttons"><button type="button" class="button button-primary"><?php esc_html_e( 'OK', 'citadela-pro' ) ?></button></div>
                    </div>
                </div>
            </div>
        </div>
        <?php } else { ?>
        <div id="uploader" class="drag-drop m-auto">
            <div class="upload-card flex">
                <div class="m-auto w-full">
                    <div id="uploader-selector" class="text-center">
                        <div><h2><?php esc_html_e( 'Drop Layout package here', 'citadela-pro' ) ?></h2></div>
                        <div><?php echo esc_html_x( 'or', 'Uploader: Drop Layout package here - or - Select Layout package', 'citadela-pro' ) ?></div>
                        <div class="confirm-buttons"><input id="uploader-browse-button" type="button" value="<?php esc_attr_e( 'Select Layout package', 'citadela-pro' ) ?>" class="button button-primary" /></div>
                    </div>
                    <div id="uploader-progress" class="hidden text-center">
                        <div><h3 class="text-gray-800"><?php _e( 'Uploading&#8230;', 'citadela-pro' ) ?></h3></div>
                        <div><progress value="42" max="100"></progress></div>
                    </div>
                    <div id="uploader-error" class="hidden">
                        <div><h3 id="uploader-error-title" class="text-gray-800"></h3></div>
                        <div id="uploader-error-msg" class="text-gray-700"></div>
                        <div class="text-center confirm-buttons"><button type="button" class="button button-primary"><?php esc_html_e( 'OK', 'citadela-pro' ) ?></button></div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <div id="import-confirmation-requirements" class="hidden m-auto">
            <div class="import-card flex">
                <div class="m-auto w-full">
                    <h2 class="text-gray-800"><?php esc_html_e( 'Required plugins', 'citadela-pro' ) ?></h2>
                    <p class="text-gray-600">
                        <?php esc_html_e( 'The Citadela Layout requires these plugins. The required plugins will be installed and activated automatically. In case you don\'t have access to one of the plugins, some pages might be incomplete.', 'citadela-pro' ) ?>
                        <ul>
                            <li id="import-requirements-aitthemes" class="hidden"><?php esc_html_e( 'From AitThemes:', 'citadela-pro' ) ?> <strong></strong></li>
                            <li id="import-requirements-wporg" class="hidden"><?php esc_html_e( 'From WordPress.org:', 'citadela-pro' ) ?> <strong></strong></li>
                        </ul>
                    </p>
                    <div class="text-center confirm-buttons">
                        <a href="#" id="import-confirm-requirements" class="button button-primary"><?php esc_html_e( 'Confirm', 'citadela-pro' ) ?></a>
                        <a href="#" id="import-cancel-requirements" class="button button-secondary"><?php esc_html_e( 'Cancel', 'citadela-pro' ) ?></a>
                    </div>
                </div>
            </div>
        </div>
        <div id="import-requirements-install" class="hidden m-auto">
            <div class="upload-card flex">
                <div class="m-auto w-full">
                    <div id="import-requirements-install-progress" class="text-center">
                        <div><h3 class="text-gray-800"><?php _e( 'Installing and activating plugins&#8230;', 'citadela-pro' ) ?></h3></div>
                        <div><progress value="100" max="100"></progress></div>
                    </div>
                    <div id="import-requirements-install-error" class="hidden">
                        <div><h3 id="import-requirements-install-error-title" class="text-gray-800"><?php esc_html_e( 'Error', 'citadela-pro' ) ?></h3></div>
                        <div id="import-requirements-install-error-msg" class="text-gray-700"></div>
                        <div class="text-center confirm-buttons"><button type="button" class="button button-primary"><?php esc_html_e( 'OK', 'citadela-pro' ) ?></button></div>
                    </div>
                </div>
            </div>
        </div>
        <div id="import-confirmation" class="hidden m-auto">
            <div class="import-card flex">
                <div class="m-auto w-full">
                    <h2 class="text-gray-800">⚠ <?php esc_html_e( 'Your current content will be deleted', 'citadela-pro' ) ?></h2>
                    <p class="text-gray-600"><?php _e( 'Import of Layout package will <strong>delete all your content</strong> - all Posts, Pages, Media, Tags and Categories.', 'citadela-pro' ) ?></p>
                    <div class="text-center confirm-buttons">
                        <a href="#" id="import-confirm" class="button button-primary"><?php esc_html_e( 'Confirm', 'citadela-pro' ) ?></a>
                        <a href="#" id="import-cancel" class="button button-secondary"><?php esc_html_e( 'Cancel', 'citadela-pro' ) ?></a>
                    </div>
                </div>
            </div>
        </div>
        <div id="import-progress" class="hidden m-auto">
            <div class="import-card flex">
                <div class="m-auto w-full">
                    <h2 class="text-gray-800"><?php _e( "We're importing the Layout package...", 'citadela-pro' ) ?></h2>
                    <p class="text-gray-600"><?php _e( 'The process can take anywhere between 30 seconds to 30 minutes depending on the size of the Layout package and speed of connection (mainly due to downloading demo images).', 'citadela-pro' ); ?></p>
                    <p class="text-gray-600"><?php _e( 'Please do not close this browser window until the Layout package is imported completely.', 'citadela-pro' ); ?></p>

                    <div id="import-status" class="text-center">
                        <div class="hidden">
                            <span id="indicator-icon-done">👍</span>
                            <span id="indicator-icon-dieded">💀</span>
                            <svg id="indicator-icon-wip" style="margin: auto; background: rgba(0, 0, 0, 0) none repeat scroll 0% 0%; shape-rendering: auto;" width="48px" height="48px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><g><circle cx="60" cy="50" r="4" fill="#00ace1"><animate attributeName="cx" repeatCount="indefinite" dur="0.5714285714285714s" values="95;35" keyTimes="0;1" begin="-1.1725s"></animate><animate attributeName="fill-opacity" repeatCount="indefinite" dur="0.5714285714285714s" values="0;1;1" keyTimes="0;0.2;1" begin="-1.1725s"></animate></circle><circle cx="60" cy="50" r="4" fill="#00ace1"><animate attributeName="cx" repeatCount="indefinite" dur="0.5714285714285714s" values="95;35" keyTimes="0;1" begin="-0.5775s"></animate><animate attributeName="fill-opacity" repeatCount="indefinite" dur="0.5714285714285714s" values="0;1;1" keyTimes="0;0.2;1" begin="-0.5775s"></animate></circle><circle cx="60" cy="50" r="4" fill="#00ace1"><animate attributeName="cx" repeatCount="indefinite" dur="0.5714285714285714s" values="95;35" keyTimes="0;1" begin="0s"></animate><animate attributeName="fill-opacity" repeatCount="indefinite" dur="0.5714285714285714s" values="0;1;1" keyTimes="0;0.2;1" begin="0s"></animate></circle></g><g transform="translate(3 0)"><path d="M50 50L20 50A30 30 0 0 0 80 50Z" fill="#9227c5" transform="rotate(90 50 50)"></path><path d="M50 50L20 50A30 30 0 0 0 80 50Z" fill="#9227c5"><animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="0.5714285714285714s" values="0 50 50;45 50 50;0 50 50" keyTimes="0;0.5;1"></animateTransform></path><path d="M50 50L20 50A30 30 0 0 1 80 50Z" fill="#9227c5"><animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="0.5714285714285714s" values="0 50 50;-45 50 50;0 50 50" keyTimes="0;0.5;1"></animateTransform></path></g></svg>
                            <svg id="indicator-icon-idle" style="margin: auto; background: rgba(0, 0, 0, 0) none repeat scroll 0% 0%; shape-rendering: auto;" width="48px" height="48px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><g><path d="M50 50L20 50A30 30 0 0 0 80 50Z" fill="#9227c5" transform="rotate(20 50 50)"></path><path d="M50 50L20 50A30 30 0 0 0 80 50Z" fill="#9227c5" transform="rotate(160 50 50)"></path></g></svg>
                        </div>

                        <div id="import-indicator-content" class="import-indicator">
                            <span class="indicator-icon"></span>
                            <div class="indicator-label text-gray-800"><?php _e( 'Content', 'citadela-pro' ) ?></div>
                        </div>
                        <div id="import-indicator-options" class="import-indicator">
                            <span class="indicator-icon"></span>
                            <div class="indicator-label text-gray-800"><?php _e( 'Settings', 'citadela-pro' ) ?></div>
                        </div>
                        <div id="import-indicator-images" class="import-indicator">
                            <span class="indicator-icon"></span>
                            <div class="indicator-label text-gray-800"><?php _e( 'Images', 'citadela-pro' ) ?></div>
                        </div>
                    </div>

                    <div id="import-error" class="text-red-700 bg-red-200"></div>
                </div>
            </div>
        </div>
        <div id="import-complete" class="hidden m-auto">
            <div class="import-card flex">
                <div class="text-center m-auto w-full">
                    <h2 class="text-gray-800"><?php esc_html_e( 'Import done!', 'citadela-pro' ) ?> 🎉</h2>
                    <div class="confirm-buttons"><a href="<?php echo home_url() ?>" class="button button-primary button-hero"><?php esc_html_e( 'Show website', 'citadela-pro' ) ?> <span class="dashicons dashicons-external"></span></a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Novo Material</title>
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #2ecc71;
            --danger-color: #e74c3c;
            --text-color: #2c3e50;
            --background-color: #f5f6fa;
            --card-background: #ffffff;
            --border-radius: 10px;
            --transition: all 0.3s ease;
        }

        .gma-create-wrap {
            padding: 20px;
            background: var(--background-color);
            min-height: 100vh;
        }

        .gma-create-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .gma-create-title {
            font-size: 2.5em;
            color: var(--text-color);
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
        }

        .gma-create-card {
            background: var(--card-background);
            border-radius: var(--border-radius);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 30px;
            animation: slideIn 0.5s ease;
        }

        .gma-form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .gma-form-group {
            margin-bottom: 20px;
        }

        .gma-form-group.full-width {
            grid-column: 1 / -1;
        }

        .gma-form-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-color);
        }

        .gma-input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e1e1;
            border-radius: var(--border-radius);
            font-size: 1em;
            transition: var(--transition);
        }

        .gma-input:focus, select:focus, textarea:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.2);
        }

        .gma-upload-container {
            display: flex;
            gap: 10px;
        }

        .gma-image-preview {
            margin-top: 10px;
            max-width: 300px;
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .gma-image-preview img {
            width: 100%;
            height: auto;
            display: block;
        }

        .gma-character-count {
            text-align: right;
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }

        .gma-button {
            padding: 12px 24px;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            text-decoration: none;
        }

        .gma-button.primary {
            background: var(--primary-color);
            color: white;
        }

        .gma-button.secondary {
            background: var(--secondary-color);
            color: white;
        }

        .gma-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .gma-form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: flex-end;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .gma-form-grid {
                grid-template-columns: 1fr;
            }
            
            .gma-form-actions {
                flex-direction: column;
            }
            
            .gma-button {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="gma-create-wrap">
        <div class="gma-create-container">
            <h1 class="gma-create-title">🎨 Criar Novo Material</h1>

            <div class="gma-create-card">
                <form method="post" class="gma-create-form" id="gma-material-form">
                    <?php wp_nonce_field('gma_novo_material', 'gma_novo_material_nonce'); ?>
                    
                    <div class="gma-form-grid">
                        <!-- Seleção de Campanha -->
                        <div class="gma-form-group">
                            <label for="campanha_id">
                                <i class="dashicons dashicons-megaphone"></i> Campanha
                            </label>
                            <select name="campanha_id" id="campanha_id" required>
                                <option value="">Selecione uma campanha</option>
                                <?php 
                                $campanhas = gma_listar_campanhas();
                                foreach ($campanhas as $campanha): 
                                    $tipo = esc_attr($campanha->tipo_campanha);
                                ?>
                                    <option value="<?php echo esc_attr($campanha->id); ?>" 
                                            data-tipo="<?php echo $tipo; ?>">
                                        <?php echo esc_html($campanha->nome); ?> 
                                        (<?php echo ucfirst($tipo); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tipo de Mídia -->
                        <div class="gma-form-group">
                            <label for="tipo_midia">
                                <i class="dashicons dashicons-format-gallery"></i> Tipo de Mídia
                            </label>
                            <select name="tipo_midia" id="tipo_midia" required>
                                <option value="imagem">Imagem Única</option>
                                <option value="carrossel">Carrossel</option>
                                <option value="video">Vídeo</option>
                            </select>
                        </div>

                        <!-- Container de Upload -->
                        <div class="gma-form-group full-width" id="upload-container">
                            <label>
                                <i class="dashicons dashicons-format-image"></i> Mídia
                            </label>
                            <div id="midia-uploads" class="midia-uploads-grid"></div>
                            <button type="button" id="add-midia" class="gma-button secondary">
                                <i class="dashicons dashicons-plus"></i> Adicionar Mídia
                            </button>
                        </div>

                        <!-- Container de Vídeo -->
                        <div class="gma-form-group full-width" id="video-container" style="display:none;">
                            <label for="video_url">
                                <i class="dashicons dashicons-video-alt3"></i> URL do Vídeo
                            </label>
                            <input type="url" name="video_url" id="video_url" class="gma-input" 
                                   placeholder="https://www.youtube.com/...">
                        </div>

                        <!-- Upload de Imagem -->
                        <div class="gma-form-group" id="imagem-group" style="display: none;">
                            <label for="gma-imagem-url">
                                <i class="dashicons dashicons-format-image"></i> Imagem
                            </label>
                            <div class="gma-upload-container">
                                <input type="text" name="imagem_url" id="gma-imagem-url" 
                                       class="gma-input" required readonly>
                                <input type="hidden" name="arquivo_id" id="gma-arquivo-id">
                                <button type="button" id="gma-upload-btn" class="gma-button secondary">
                                    <i class="dashicons dashicons-upload"></i> Selecionar
                                </button>
                            </div>
                            <div id="gma-image-preview" class="gma-image-preview"></div>
                        </div>

                        <!-- Copy do Material -->
                        <div class="gma-form-group full-width">
                            <label for="copy">
                                <i class="dashicons dashicons-editor-paste-text"></i> Copy
                            </label>
                            <textarea name="copy" id="copy" rows="5" required></textarea>
                            <div class="gma-character-count">
                                <span id="char-count">0</span> caracteres
                            </div>
                            <div class="gma-form-group full-width">
                                <button type="button" id="get-suggestions" class="gma-button secondary">
                                    <i class="dashicons dashicons-admin-customizer"></i> Obter Sugestões AI
                                </button>
                                <div id="suggestions-container" style="display: none;">
                                    <h3>Sugestões da IA</h3>
                                    <div id="suggestions-content"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Link do Canva (apenas para campanhas de marketing) -->
                        <div class="gma-form-group full-width" id="canva-group" style="display: none;">
                            <label for="link_canva">
                                <i class="dashicons dashicons-art"></i> Link do Canva
                            </label>
                            <input type="url" name="link_canva" id="link_canva" 
                                   class="gma-input" placeholder="https://www.canva.com/...">
                        </div>
                    </div>

                    <div class="gma-form-actions">
                        <button type="submit" name="criar_material" class="gma-button primary">
                            <i class="dashicons dashicons-saved"></i> Criar Material
                        </button>
                        <a href="<?php echo admin_url('admin.php?page=gma-materiais'); ?>" 
                           class="gma-button secondary">
                            <i class="dashicons dashicons-arrow-left-alt"></i> Voltar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        jQuery(document).ready(function($) {
            // Controle de exibição dos campos baseado no tipo de campanha
            $('#campanha_id').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var tipoCampanha = selectedOption.data('tipo');
                
                if (tipoCampanha === 'marketing') {
                    $('#canva-group').show();
                    $('#link_canva').prop('required', false);
                } else {
                    $('#canva-group').hide();
                    $('#link_canva').prop('required', false);
                }
            });

            // Upload de imagem
            $('#gma-upload-btn').click(function(e) {
                e.preventDefault();
                
                var custom_uploader = wp.media({
                    title: 'Selecionar Imagem',
                    button: {
                        text: 'Usar esta imagem'
                    },
                    multiple: false
                });

                custom_uploader.on('select', function() {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    $('#gma-imagem-url').val(attachment.url);
                    $('#gma-arquivo-id').val(attachment.id);
                    $('#gma-image-preview').html('<img src="' + attachment.url + '" alt="Preview">');
                });

                custom_uploader.open();
            });

            // Controle de exibição dos campos de mídia
            $('#tipo_midia').on('change', function() {
                var tipo = $(this).val();
                if (tipo === 'video') {
                    $('#upload-container').hide();
                    $('#video-container').show();
                    $('#video_url').prop('required', true);
                    $('#imagem-group').hide();
                } else if (tipo === 'imagem') {
                    $('#upload-container').hide();
                    $('#video-container').hide();
                    $('#video_url').prop('required', false);
                    $('#imagem-group').show();
                } else {
                    $('#upload-container').show();
                    $('#video-container').hide();
                    $('#video_url').prop('required', false);
                    $('#imagem-group').hide();
                }
            });

            // Upload de mídia (para carrossel ou vídeo)
            let mediaUploader;
            $('#add-midia').on('click', function(e) {
                e.preventDefault();
                
                if (!mediaUploader) {
                    mediaUploader = wp.media({
                        title: 'Selecionar Mídia',
                        button: {
                            text: 'Usar esta mídia'
                        },
                        multiple: $('#tipo_midia').val() === 'carrossel' // Permite múltiplas imagens para carrossel
                    });
                }

                mediaUploader.on('select', function() {
                    const attachments = mediaUploader.state().get('selection').toJSON();
                    
                    if ($('#tipo_midia').val() === 'imagem' && attachments.length > 1) {
                        alert('Selecione apenas uma imagem para o tipo "Imagem Única"');
                        return;
                    }

                    $('#midia-uploads').empty();
                    
                    attachments.forEach(attachment => {
                        const preview = `
                            <div class="midia-item">
                                <img src="${attachment.url}" alt="Preview">
                                <input type="hidden" name="midias[]" value="${attachment.url}">
                                <button type="button" class="remove-midia">×</button>
                            </div>
                        `;
                        $('#midia-uploads').append(preview);
                    });
                });

                mediaUploader.open();
            });

            // Remover mídia
            $(document).on('click', '.remove-midia', function() {
                $(this).closest('.midia-item').remove();
            });

            // Sugestões AI
            $('#get-suggestions').on('click', function() {
                const copy = $('#copy').val();
                if (!copy) {
                    alert('Por favor, insira algum texto primeiro.');
                    return;
                }
                
                const button = $(this);
                button.prop('disabled', true).text('Obtendo sugestões...');
                
                $.ajax({
                    url: gma_ajax.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'gma_get_copy_suggestions',
                        nonce: gma_ajax.nonce,
                        copy: copy
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#suggestions-content').html(response.data.suggestions);
                            $('#suggestions-container').slideDown();
                        } else {
                            alert('Falha ao obter sugestões. Tente novamente.');
                        }
                    },
                    error: function() {
                        alert('Erro ao conectar com o servidor.');
                    },
                    complete: function() {
                        button.prop('disabled', false).text('Obter Sugestões AI');
                    }
                });
            });

            // Validação do formulário
            $('#gma-material-form').on('submit', function(e) {
                let isValid = true;
                const tipo = $('#tipo_midia').val();
                
                if ((tipo === 'imagem' || tipo === 'carrossel') && !$('.midia-item').length) {
                    alert('Adicione pelo menos uma imagem.');
                    isValid = false;
                }
                
                $(this).find('[required]').each(function() {
                    if (!$(this).val()) {
                        isValid = false;
                        $(this).addClass('error').shake();
                    } else {
                        $(this).removeClass('error');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    alert('Por favor, preencha todos os campos obrigatórios.');
                }
            });

            // Efeito shake para campos com erro
            $.fn.shake = function() {
                this.each(function() {
                    $(this).css('position', 'relative');
                    for(var i = 0; i < 3; i++) {
                        $(this).animate({left: -10}, 50)
                               .animate({left: 10}, 50)
                               .animate({left: 0}, 50);
                    }
                });
            };
        });
    </script>

    <?php wp_footer(); ?>
</body>
</html>
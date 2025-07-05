<style>
    .produto-card {
        border: 1px solid #ddd;
        border-radius: 16px;
        padding: 10px 8px 18px 8px;
        margin-bottom: 15px;
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: transform 0.2s;
        position: relative;
        overflow: hidden;
        width: 250px;
        min-height: 420px;
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-left: auto;
        margin-right: auto;
    }
    
    .produto-card:hover {
        transform: translateY(-2px) scale(1.03);
        box-shadow: 0 6px 16px rgba(0,0,0,0.13);
    }
    
    .produto-imagem {
        width: 240px;
        height: 320px;
        object-fit: cover;
        border-radius: 12px;
        margin-bottom: 12px;
        cursor: pointer;
        transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s;
        border: 2px solid #f0f0f0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        background: #fafafa;
        display: block;
    }
    
    .produto-imagem:hover {
        transform: scale(1.08);
        box-shadow: 0 8px 24px rgba(0,0,0,0.18);
        border-color: #007bff;
    }
    
    .produto-titulo {
        font-size: 17px;
        font-weight: bold;
        margin-bottom: 6px;
        color: #333;
        text-align: center;
        min-height: 38px;
    }
    
    .produto-descricao {
        color: #666;
        margin-bottom: 8px;
        line-height: 1.4;
        font-size: 13px;
        text-align: center;
        min-height: 36px;
    }
    
    .produto-preco {
        font-size: 19px;
        font-weight: bold;
        color: #28a745;
        margin-bottom: 8px;
        text-align: center;
    }
    
    .produto-categoria {
        background: #007bff;
        color: white;
        padding: 3px 7px;
        border-radius: 4px;
        font-size: 12px;
        display: inline-block;
        margin-bottom: 7px;
    }
    
    .produto-modelo {
        color: #666;
        font-size: 13px;
        margin-bottom: 7px;
        text-align: center;
    }
    
    .recomendado-badge {
        background: #ffc107;
        color: #212529;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        position: absolute;
        top: 10px;
        right: 10px;
    }
    
    .section-title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
        color: #333;
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
    }
    
    .no-products {
        text-align: center;
        padding: 40px;
        color: #666;
        font-size: 16px;
    }
    
    /* Modal para visualização da imagem */
    .modal-imagem {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.9);
        backdrop-filter: blur(5px);
    }
    
    .modal-conteudo {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        max-width: 90%;
        max-height: 90%;
        text-align: center;
    }
    
    .modal-imagem img {
        max-width: 90vw;
        max-height: 90vh;
        width: auto;
        height: auto;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        background: #222;
        margin: auto;
        display: block;
    }
    
    .fechar-modal {
        position: absolute;
        top: 15px;
        right: 35px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        z-index: 10000;
    }
    
    .fechar-modal:hover {
        color: #bbb;
    }
    
    @media (max-width: 600px) {
        .produto-card {
            width: 98vw;
            min-height: 350px;
        }
        .produto-imagem {
            width: 80vw;
            height: 48vw;
            min-width: 120px;
            min-height: 180px;
        }
    }
</style>

<div class="span12" style="margin-left: 0">
    <div class="widget-box">
        <div class="widget-title" style="margin: -20px 0 0">
            <span class="icon"><i class="fas fa-store"></i></span>
            <h5>Nossa Loja</h5>
        </div>
        <div class="widget-content">
            
            <!-- Produtos Recomendados -->
            <?php if (!empty($produtos_recomendados)): ?>
                <div class="row-fluid">
                    <div class="span12">
                        <h3 class="section-title">
                            <i class="fas fa-star" style="color: #ffc107;"></i> 
                            Produtos Recomendados para Você
                        </h3>
                    </div>
                </div>
                
                <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: flex-start;">
                    <?php foreach ($produtos_recomendados as $produto): ?>
                        <div style="flex: 0 0 auto;">
                            <div class="produto-card">
                                <span class="recomendado-badge">
                                    <i class="fas fa-star"></i> Recomendado
                                </span>
                                
                                <?php if (!empty($produto->primeira_imagem)): ?>
                                    <img src="<?php echo $produto->primeira_imagem; ?>" 
                                         alt="<?php echo $produto->descricao; ?>" 
                                         class="produto-imagem"
                                         onclick="abrirModal('<?php echo $produto->imagem_completa ?? $produto->primeira_imagem; ?>', '<?php echo $produto->descricao; ?>')">
                                <?php else: ?>
                                    <img src="<?php echo base_url('assets/img/no-image.png'); ?>" 
                                         alt="Sem imagem" 
                                         class="produto-imagem">
                                <?php endif; ?>
                                
                                <div class="produto-titulo"><?php echo $produto->descricao; ?></div>
                                
                                <?php if ($produto->nomeModelo): ?>
                                    <div class="produto-modelo">
                                        <strong>Modelo:</strong> <?php echo $produto->nomeModelo; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="produto-preco">
                                    R$ <?php echo number_format($produto->precoVenda, 2, ',', '.'); ?>
                                </div>
                                
                                <div style="text-align: center;">
                                    <span class="badge badge-info">
                                        Similaridade: <?php echo round($produto->similaridade, 1); ?>%
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <hr style="margin: 30px 0;">
            <?php endif; ?>
            

            
            <!-- Produtos das Suas Categorias de Interesse -->
            <div class="row-fluid">
                <div class="span12">
                    <h3 class="section-title">
                        <i class="fas fa-shopping-bag"></i> 
                        Produtos das Suas Categorias de Interesse
                    </h3>
                </div>
            </div>
            
            <?php if (!empty($produtos_gerais)): ?>
                <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: flex-start;">
                    <?php foreach ($produtos_gerais as $produto): ?>
                        <div style="flex: 0 0 auto;">
                            <div class="produto-card">
                                <?php if (!empty($produto->primeira_imagem)): ?>
                                    <img src="<?php echo $produto->primeira_imagem; ?>" 
                                         alt="<?php echo $produto->descricao; ?>" 
                                         class="produto-imagem"
                                         onclick="abrirModal('<?php echo $produto->imagem_completa ?? $produto->primeira_imagem; ?>', '<?php echo $produto->descricao; ?>')">
                                <?php else: ?>
                                    <img src="<?php echo base_url('assets/img/no-image.png'); ?>" 
                                         alt="Sem imagem" 
                                         class="produto-imagem">
                                <?php endif; ?>
                                
                                <div class="produto-titulo"><?php echo $produto->descricao; ?></div>
                                
                                <?php if ($produto->nomeModelo): ?>
                                    <div class="produto-modelo">
                                        <strong>Modelo:</strong> <?php echo $produto->nomeModelo; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="produto-preco">
                                    R$ <?php echo number_format($produto->precoVenda, 2, ',', '.'); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-products">
                    <i class="fas fa-box-open" style="font-size: 48px; color: #ddd; margin-bottom: 20px;"></i>
                    <p>Nenhum produto disponível para o seu tipo de interesse.</p>
                    <p>Entre em contato conosco para atualizar seu perfil.</p>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>

<!-- Modal de visualização de imagem -->
<div id="modalImagem" class="modal-imagem">
    <span class="fechar-modal" onclick="fecharModal()">&times;</span>
    <div class="modal-conteudo">
        <img id="imagemModal" src="" alt="Imagem do Produto">
        <div id="descricaoModal" style="color: #fff; margin-top: 10px; font-size: 18px;"></div>
    </div>
</div>

<script>
function abrirModal(src, descricao) {
    document.getElementById('imagemModal').src = src;
    document.getElementById('descricaoModal').innerText = descricao;
    document.getElementById('modalImagem').style.display = 'block';
}
function fecharModal() {
    document.getElementById('modalImagem').style.display = 'none';
    document.getElementById('imagemModal').src = '';
}
// Fechar ao clicar fora da imagem
window.onclick = function(event) {
    var modal = document.getElementById('modalImagem');
    if (event.target === modal) {
        fecharModal();
    }
}
</script> 
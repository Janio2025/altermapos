<style>
    .produto-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    
    .produto-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .produto-imagem {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 4px;
        margin-bottom: 10px;
    }
    
    .produto-titulo {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 8px;
        color: #333;
    }
    
    .produto-descricao {
        color: #666;
        margin-bottom: 10px;
        line-height: 1.4;
    }
    
    .produto-preco {
        font-size: 20px;
        font-weight: bold;
        color: #28a745;
        margin-bottom: 10px;
    }
    
    .produto-categoria {
        background: #007bff;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        display: inline-block;
        margin-bottom: 10px;
    }
    
    .produto-modelo {
        color: #666;
        font-size: 14px;
        margin-bottom: 10px;
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
                
                <div class="row-fluid">
                    <?php foreach ($produtos_recomendados as $produto): ?>
                        <div class="span3" style="position: relative;">
                            <div class="produto-card">
                                <span class="recomendado-badge">
                                    <i class="fas fa-star"></i> Recomendado
                                </span>
                                
                                <?php if (!empty($produto->primeira_imagem)): ?>
                                    <img src="<?php echo $produto->primeira_imagem; ?>" 
                                         alt="<?php echo $produto->descricao; ?>" 
                                         class="produto-imagem">
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
                                
                                <?php if ($produto->nome_categoria): ?>
                                    <div class="produto-categoria"><?php echo $produto->nome_categoria; ?></div>
                                <?php endif; ?>
                                
                                <div class="produto-descricao">
                                    <?php echo substr($produto->descricao, 0, 100); ?>
                                    <?php if (strlen($produto->descricao) > 100) echo '...'; ?>
                                </div>
                                
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
                <div class="row-fluid">
                    <?php foreach ($produtos_gerais as $produto): ?>
                        <div class="span3">
                            <div class="produto-card">
                                <?php if (!empty($produto->primeira_imagem)): ?>
                                    <img src="<?php echo $produto->primeira_imagem; ?>" 
                                         alt="<?php echo $produto->descricao; ?>" 
                                         class="produto-imagem">
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
                                
                                <?php if ($produto->nome_categoria): ?>
                                    <div class="produto-categoria"><?php echo $produto->nome_categoria; ?></div>
                                <?php endif; ?>
                                
                                <div class="produto-descricao">
                                    <?php echo substr($produto->descricao, 0, 100); ?>
                                    <?php if (strlen($produto->descricao) > 100) echo '...'; ?>
                                </div>
                                
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
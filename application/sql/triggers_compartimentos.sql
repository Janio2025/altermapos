DELIMITER //

CREATE TRIGGER after_insert_compartimento_equipamento
AFTER INSERT ON compartimento_equipamentos
FOR EACH ROW
BEGIN
    -- Registrar a alteração no log
    INSERT INTO log_compartimentos (
        compartimento_id,
        acao,
        item_id,
        tipo_item,
        data_alteracao,
        usuario_id
    ) VALUES (
        NEW.compartimento_id,
        'adicionar',
        COALESCE(NEW.produtos_id, NEW.os_id),
        CASE 
            WHEN NEW.produtos_id IS NOT NULL THEN 'produto'
            ELSE 'equipamento'
        END,
        NOW(),
        @usuario_id
    );
END//

CREATE TRIGGER after_delete_compartimento_equipamento
AFTER DELETE ON compartimento_equipamentos
FOR EACH ROW
BEGIN
    -- Registrar a alteração no log
    INSERT INTO log_compartimentos (
        compartimento_id,
        acao,
        item_id,
        tipo_item,
        data_alteracao,
        usuario_id
    ) VALUES (
        OLD.compartimento_id,
        'remover',
        COALESCE(OLD.produtos_id, OLD.os_id),
        CASE 
            WHEN OLD.produtos_id IS NOT NULL THEN 'produto'
            ELSE 'equipamento'
        END,
        NOW(),
        @usuario_id
    );
END//

DELIMITER ; 
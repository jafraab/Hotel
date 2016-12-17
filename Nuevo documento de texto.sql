DELIMITER $$
CREATE DEFINER=`hotel`@`%` PROCEDURE `sp_hot_regpas_ins`(IN `p_id_cliente` VARCHAR(32), IN `p_dias_estadia` INT, IN `p_hora_ingreso` VARCHAR(16), IN `p_fecha_ingreso` VARCHAR(10), IN `p_habitacion` INT, IN `p_valor_pp` INT, OUT `p_id_registro` INT, IN `p_patente` VARCHAR(8))
BEGIN
	    select 
    ifnull(max(id_registro),
	concat(concat(extract(year from current_date()),lpad(dayofyear(current_date()), 3, '0')),'000')) + 1 into p_id_registro from hot_regpas;

	INSERT INTO hotel.hot_regpas
	(
    id_cliente,
	dias_estadia,
	hora_ingreso,
	fecha_ingreso,
	habitacion,
	valor_pp,
	id_registro,
    patente
    )
	VALUES
	(
    p_id_cliente,
	p_dias_estadia,
	p_hora_ingreso,
	p_fecha_ingreso,
	p_habitacion,
	p_valor_pp,
	p_id_registro,
    p_patente
    );
END$$
DELIMITER ;
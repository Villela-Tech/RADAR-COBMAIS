CREATE TABLE referencias_cobrancas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_radar VARCHAR(50) NOT NULL,
    id_cobmais INT NOT NULL,
    numero_acordo VARCHAR(50) NOT NULL,
    status VARCHAR(20) NOT NULL,
    data_criacao DATETIME NOT NULL,
    data_pagamento DATETIME NULL,
    ultima_atualizacao DATETIME NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    observacao TEXT NULL,
    UNIQUE KEY uk_id_cobmais (id_cobmais),
    INDEX idx_id_radar (id_radar),
    INDEX idx_radar_ativo (id_radar, ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 
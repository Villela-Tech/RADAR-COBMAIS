# Projeto Interno – Integração Tripla entre Radar WK, CobMais e Bitrix24

## 📝 Resumo

Este projeto tem como objetivo desenvolver uma **integração robusta** entre três sistemas utilizados pela empresa:

- **Radar WK** (CRM / sistema de faturamento)
- **CobMais** (plataforma de cobrança)
- **Bitrix24** (CRM comercial)

A finalidade é manter os três ambientes sincronizados, garantindo que as informações fluam corretamente entre os setores de faturamento, cobrança e comercial.

---

## 🔄 Visão do Projeto

- As **cobranças criadas no Radar WK** devem ser automaticamente enviadas ao **CobMais** para emissão.
- O **retorno de status de pagamento** (pago, vencido, cancelado etc.) deve ser recebido do CobMais e atualizado no Radar WK.
- As **informações de cobrança e cliente** devem também aparecer no **Bitrix24**, possibilitando o acompanhamento comercial.
- Integração preferencialmente em **tempo real** via **webhooks** ou sincronização periódica (ex: a cada 5 ou 10 minutos).
- Necessário desenvolver um **middleware** para:
  - Intermediar as integrações
  - Tratar falhas e gerar logs
  - Permitir reenvios manuais de dados com erro

---

## 🧠 Nível Técnico Recomendado

### 1. Desenvolvedor Backend (Pleno ou Sênior)

**Requisitos:**
- Experiência sólida com **APIs REST** (consumo e criação)
- **Autenticação via token/OAuth2**
- Integrações entre sistemas distintos
- Desenvolvimento de **middleware backend** (Node.js, PHP, Python etc.)
- Tratamento de erros e **logs estruturados**
- Banco de dados relacionais (**PostgreSQL**, **MySQL**)
- Desejável: uso de **Redis**, **filas**, controle de falhas

---

### 2. Desenvolvedor de Integrações / Full Stack (Pleno ou Sênior)

**Requisitos:**
- Leitura e implementação de **documentação de APIs de terceiros**
- Mapeamento e transformação de dados entre diferentes formatos
- Automatização de integrações via scripts ou ferramentas low-code
- Familiaridade com **Postman, Insomnia, Git**
- Boas práticas de versionamento e documentação técnica
- Desejável: conhecimento em **n8n**, **Make (Integromat)** ou **Zapier**

---

## ⚠️ Considerações Finais

- Os desenvolvedores devem ter **autonomia técnica** para estudar documentações e validar fluxos de integração.
- É essencial prever **tratamento de falhas** (timeouts, erros de API, dados inconsistentes).
- Testes **ponta a ponta** devem ser realizados antes da liberação do projeto.
- A equipe de desenvolvimento deve alinhar-se com os setores de **Cobrança, TI e Comercial** para garantir a lógica dos fluxos.

---

> **Nota:** Este documento pode ser expandido com diagramas de arquitetura, cronogramas, exemplos de payloads, e endpoints técnicos conforme evolução do projeto.

<?php

echo substr(uniqid(), 0, 12) . "\n";

//// secretaire / 527e8849dce0
//// president / 527e8858137d
//// tresorier / 527e88a24ec5
//// ce / 527e88a9c300
//// ccie / 527e88aec967
//// cpi / 527e8816054a
//// no-reply / 527e88b384f3

// Serveur POP3 : pop3.adcog.fr
// Serveur SMTP : smtp.adcog.fr

// Nom du serveur entrant : pop3.adcog.fr
// Port entrant : 110

// Nom du serveur sortant : mail.adcog.fr
// Port sortant : 587 (au lieu de 25 car ce port est filtré chez de nombreux FAI)

// Vous devez aussi cocher l'option "serveur d'envoi/smtp requiert une authentification"
// (pas SSL ni cryptée) en utilisant les mêmes paramètres que pour le serveur entrant POP.

// https://ssl0.ovh.net/horde/imp/

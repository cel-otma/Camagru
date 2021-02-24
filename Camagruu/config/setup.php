<?php
    include "database.php";
// session_start();
    $db = new PDO("mysql:host=".$DB_HOST, $DB_USER, $DB_PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // $db->setFetchMode(PDO::FETCH_ASSOC);
    try {
        $stmt = $db->prepare("CREATE DATABASE IF NOT EXISTS `".$DB_NAME."`;");
        $stmt->execute();
    } catch (Exception $e) {
        echo "ERROR ON CREATE DATABASE";
    }
    try {
        $stmt = $db->prepare("USE`".$DB_NAME."`;");
        $stmt->execute();
    } catch (Exception $e) {
        echo "ERROR ON CREATE DATABASE";
    }
    $db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);

    try {
        $stmt = $db->prepare("CREATE TABLE IF NOT EXISTS `user`
        (
            `user_id` int  PRIMARY KEY AUTO_INCREMENT,
            `email` varchar(255),
            `username` varchar(255),
            `password` varchar(255),
            `phone_number` varchar(255),
            `notification` BOOLEAN,
            `verived` varchar(255),
            `verived_password` varchar(255)
        );");
        $stmt->execute();
    } catch (Exception $e) {
        echo "ERROR ON TABLE account";
    }
    try {
        $stmt = $db->prepare("CREATE TABLE IF NOT EXISTS `post`
        (
            `id_post` int PRIMARY KEY AUTO_INCREMENT,
            `username` varchar(255),
            `image` varchar(255),
            `date_creation` datetime NOT NULL
        );");
        $stmt->execute();
    } catch (Exception $e) {
        echo "ERROR ON TABLE account";
    }

    try {
        $stmt = $db->prepare("CREATE TABLE IF NOT EXISTS `comment`
        (
            `id_comment` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `username` varchar(255),
            `comment` varchar(255),
            `time_comemmt` datetime NOT NULL,
            `id_post` varchar(255)
        );");
        $stmt->execute();
    } catch (Exception $e) {
        echo "ERROR ON TABLE COMMENT";
    }


    try {
        $stmt = $db->prepare("CREATE TABLE IF NOT EXISTS `like`
        (
            `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `username` varchar(255),
            `id_post` varchar(255),
            `verifed` varchar(255)
        );");
        $stmt->execute();
    } catch (Exception $e) {
        echo "ERROR ON TABLE LIKE";
    }
?>

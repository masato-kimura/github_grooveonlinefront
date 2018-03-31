<?php
namespace util;

interface MoveFileStrategy {

    // ディレクトリの確認
    public function chkdir($path);

    // ディレクトリを作成
    public function mkdir($dirName);

    // ファイルを削除
    public function del($path);

    // 画像を転送する
    public function upload($fromPath, $toPath);

}
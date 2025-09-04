## udemy Laravel講座

## ダウンロードの方法
git clone https://github.com/miya-summer/udemy.laravel.2025.part2.git

もしくはzipファイルでDLしてください。

## インストールの方法

cd udemy_laravel202502

composer install

npm install

npm run dev

.env.exampleをコピーして.env作成

DB_CONNECTION、DB_HOST、DB_PORT、DB_DATABASE、DB_USERNAME、DB_PASSWORDを設定

DB起動して

php artisan migrate:fresh --seed

と実行して、データベーステーブルとダミーデータが作成されればOK

最後に
php artisan key:generate
と入力してキーを生成

php artisan serveでサーバー起動

## インストール後の実施事項

画像のダミーデータは
public/imagesフォルダ内に
sample1.jpg ～ sample6.jpgとして
保存しています。

php artisan storage:link で
storageフォルダにリンク後、

storage/app/public/productsフォルダ内に
保存すると表示されます。
（productsフォルダがない場合は、作成してください。）

ショップの画像も表示する場合は
storage/app/public/shopsフォルダを作成し
画像を保存してください。

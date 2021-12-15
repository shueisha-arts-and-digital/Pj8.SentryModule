# Pj8.SentryModule

[Sentry](https://docs.sentry.io/platforms/php/) を [BEAR.Sunday](http://bearsunday.github.io/) アプリケーションで利用するためのモジュール

![Continuous Integration](https://github.com/pj8/pj8.sentrymodule/workflows/Continuous%20Integration/badge.svg)

## 機能

* BEAR.Sunday アプリケーションでの [Sentry PHP SDK](https://github.com/getsentry/sentry-php) の設定
* Sentry のエラー監視、パフォーマンスモニタリングへの統合を提供

## インストール

[Composer](https://getcomposer.org/) でプロジェクトにインストールします。

```bash
composer require pj8/sentry-module
```

## アプリケーションへの適用

- モジュールインストール

```php
use Ray\Di\AbstractModule;
use Pj8\SentryModule\SentryModule;

class AppModule extends AbstractModule
{
    protected function configure()
    {
        $this->install(new SentryModule(['dsn' => 'https://xxx@xxx.sentry.io/xxx"']));
    }
}
```

- エラーキャプチャー

エラーキャプチャー機能を有効化するには `SentryErrorModule` をインストールします。
SentryErrorModule は下記のインターフェイスの束縛を上書きします。

- `\BEAR\Sunday\Extension\Error\ErrorInterface`
- `\BEAR\Sunday\Extension\Error\ThrowableHandlerInterface`

そのため、既にプロジェクト独自のエラーハンドラーが束縛されている場合は SentryErrorModule のエラーキャプチャー機能が動作しない場合があります。
束縛の順序やコンテキストごとのモジュール設定など確認してください。

例：
```php
use BEAR\Package\AbstractAppModule;
use BEAR\Package\Context\ProdModule as PackageProdModule;
use Pj8\SentryModule\SentryErrorModule;

class ProdModule extends AbstractAppModule
{
    protected function configure()
    {
        $this->install(new PackageProdModule());
        $this->install(new SentryErrorModule());
    }
}
```

## パフォーマンスモニタリング

- パフォーマンスオプションを設定した場合、BEARリソースの処理時間が計測されます
- `Monitorable` アノテーションを使うと任意の処理を計測することもできます

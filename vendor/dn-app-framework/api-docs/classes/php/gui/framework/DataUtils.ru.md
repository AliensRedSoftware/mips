# DataUtils

- **класс** `DataUtils` (`php\gui\framework\DataUtils`)
- **пакет** `framework`
- **исходники** `php/gui/framework/DataUtils.php`

**Описание**

Class DataUtils

---

#### Статичные Методы

- `DataUtils ::`[`scanAll()`](#method-scanall)
- `DataUtils ::`[`scan()`](#method-scan)
- `DataUtils ::`[`cleanup()`](#method-cleanup)
- `DataUtils ::`[`_cleanup()`](#method-_cleanup)
- `DataUtils ::`[`get()`](#method-get)
- `DataUtils ::`[`getNode()`](#method-getnode)
- `DataUtils ::`[`remove()`](#method-remove)

---

#### Методы

- `->`[`__construct()`](#method-__construct)

---
# Статичные Методы

<a name="method-scanall"></a>

### scanAll()
```php
DataUtils::scanAll(php\gui\UXParent $layout, callable $callback): void
```

---

<a name="method-scan"></a>

### scan()
```php
DataUtils::scan(php\gui\UXParent $layout, callable $callback): void
```

---

<a name="method-cleanup"></a>

### cleanup()
```php
DataUtils::cleanup(php\gui\UXParent $parent): void
```

---

<a name="method-_cleanup"></a>

### _cleanup()
```php
DataUtils::_cleanup(php\gui\UXParent $parent, array $exists): UXData[]
```

---

<a name="method-get"></a>

### get()
```php
DataUtils::get(php\gui\UXNode $node, php\gui\UXParent $layout, bool $create): UXData
```

---

<a name="method-getnode"></a>

### getNode()
```php
DataUtils::getNode(php\gui\UXParent $layout, php\gui\UXData $data): UXNode
```

---

<a name="method-remove"></a>

### remove()
```php
DataUtils::remove(php\gui\UXNode $node): void
```

---
# Методы

<a name="method-__construct"></a>

### __construct()
```php
__construct(): void
```
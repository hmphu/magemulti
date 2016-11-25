# MAGENTO MULTI CLIENTS

> Something that similar to Magento Go service :)
> 
> This extension allow you host multiple clients on the same Magento codebase.
> Every single client has their own local.xml and modules/*.xml directory. That means you can setting separate databases, caching services and modules per clients.

## I. Installation

### Via modman

```bash
modman clone https://github.com/hmphu/magemulti.git
```

### Via composer

```json
{
    "require": {
        "hmphu/magemulti": "*",
    },
    "repositories": [
        {
            "type": "vcs",
            "url":  "https://github.com/hmphu/magemulti.git"
        }
    ]
}
```

## II. Structure

### Config directories

```
{root}/app/
```

### Media directories

```
```

## III. Server settings

# Esquema base (`mysql-schema.dump`)

Esquema completo del CRM **sin datos** (solo las tablas, llaves, índices, rutinas y la lista de migraciones aplicadas).
Lo usa `php artisan migrate` cuando la base está vacía: primero carga este archivo y luego aplica las migraciones
que no estén en su lista. Con él ya no hace falta el volcado con datos para instalar el CRM.

Regenerarlo (en local, con la base al día):

```bash
PATH=/opt/homebrew/opt/mysql@9.6/bin:$PATH php artisan schema:dump
sed -i '' -E 's/DEFINER=`[^`]+`@`[^`]+` //g' database/schema/mysql-schema.dump   # sin DEFINER: así carga en cualquier servidor
```

No lleva datos personales: solo estructura. Las filas de `migrations` que trae son las que ya estaban aplicadas al generarlo.

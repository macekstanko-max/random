# Personal IT Profile (GitHub Pages)

Tento repozitář obsahuje jednoduchý osobní IT profil vytvořený v GitHub Codespaces.

## Co je zde

- `index.html`: osobní IT profil (kdo jsem, co umím, co mě baví, kam směřuji)
- `styles.css`: styling
- `.github/workflows/pages.yml`: GitHub Actions pro nasazení GitHub Pages

## Jak spustit lokálně

```bash
git clone https://github.com/macekstanko-max/random
cd random
python3 -m http.server 8000
```

Pak otevřít `http://localhost:8000`.

## Publikace

Po úspěšném nasazení jsou stránky dostupné na:

`https://macekstanko-max.github.io/random`

## PHP verze

Tento web nyní podporuje PHP server-side verzi pomocí `index.php`, které načítá data z `profile.json` a vykresluje HTML bez JavaScriptu.

### Spuštění v Codespaces nebo lokálně

```bash
cd random
php -S 0.0.0.0:8000
```

Otevři `http://localhost:8000`.

> Pokud používáš GitHub Pages, musíš mít statickou stránku. Tuto PHP verzi na GitHub Pages nenasazujeme.


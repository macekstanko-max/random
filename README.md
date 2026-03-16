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

## Dynamický profil

Tento web načítá obsah (jméno, skills, interests, projekty) dynamicky ze souboru `profile.json` přes `app.js`.

> Pokud je momentálně 404, aktivujte GitHub Pages ve Settings → Pages a vyberte `gh-pages` branch / `/`.

import { useState,useEffect } from 'react';
import ID from '../../../lang/id.json';
import EN from '../../../lang/en.json';

function useLocalization() {
  const country = document.getElementById('root').getAttribute('locale')
  const locale = country == 'Indonesia' ? 'id' : 'en';

  const [lang, setLang] = useState(locale || 'en');
  const translations = lang === "id" ? ID : EN;
  

  function t(key) {
    return translations[key] || key; 
  }

  useEffect(() => {
    setLang(country === 'Indonesia' ? 'id' : 'en');
  }, [country]);

  return { t, lang, setLang, locale };
}

export default useLocalization;

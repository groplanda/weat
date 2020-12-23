export function Tabs(selector) {
  const tabs = document.querySelectorAll(selector);

  if(tabs) {
    tabs.forEach(tab => {
      tab.addEventListener('click', handleTab);
    });
    function handleTab(e) {
      const target= e.target;
      tabs.forEach(elem => {
        document.getElementById(elem.dataset.tab).style.display = 'none';
        elem.classList.remove('active')
      });
      target.classList.add('active');
      document.getElementById(target.dataset.tab).style.display = 'block';
    }
  }
}
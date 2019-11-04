module.exports = {
  title: 'PNC timetable',
  description: 'documentation for timetable',
  base: '/pnc-timetables/',
  dest: '../public',
  themeConfig: {
    nav: [
      { text: 'Home', link: '/' },
      { text: 'Documentation', link: '/documentation/' },
      { text: 'Deploy The project', link: '/deploy/' },
      { text: 'Code source', link: 'https://gitlab.com/passerelles-numeriques/pnc-timetables' },
    ],
    sidebar: {
      '/documentation/': [
        '',
        'calendar',
        'print',
        'admin',
        'stats'
      ],
      '/deploy/': [
        '',
      ]
    }
  }
}

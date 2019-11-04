export default class Teacher {
  constructor(name = '', id = 0, googleId = '') {
    this.name = name;
    this.id = id;
    this.googleId = googleId;
  }

  equals(teacher) {
    return this.id === teacher.id &&
      this.name === teacher.name &&
      this.googleId === teacher.googleId;
  }
}

import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import utc from 'dayjs/plugin/utc';
import timezone from 'dayjs/plugin/timezone';
import localizedFormat from 'dayjs/plugin/localizedFormat';
import customParseFormat from 'dayjs/plugin/customParseFormat';
import isSameOrBefore from 'dayjs/plugin/isSameOrBefore';
import isSameOrAfter from 'dayjs/plugin/isSameOrAfter';
import isBetween from 'dayjs/plugin/isBetween';
import weekOfYear from 'dayjs/plugin/weekOfYear';
import quarterOfYear from 'dayjs/plugin/quarterOfYear';

// Add plugins
dayjs.extend(relativeTime);
dayjs.extend(utc);
dayjs.extend(timezone);
dayjs.extend(localizedFormat);
dayjs.extend(customParseFormat);
dayjs.extend(isSameOrBefore);
dayjs.extend(isSameOrAfter);
dayjs.extend(isBetween);
dayjs.extend(weekOfYear);
dayjs.extend(quarterOfYear);

// Create a moment-compatible API
const dayjsWithMomentCompat = (date, format) => {
  if (format) {
    return dayjs(date, format);
  }
  return dayjs(date);
};

// Add moment-compatible methods
dayjsWithMomentCompat.utc = (date, format) => {
  if (format) {
    return dayjs.utc(date, format);
  }
  return dayjs.utc(date);
};

dayjsWithMomentCompat.tz = (date, timezone) => {
  return dayjs(date).tz(timezone);
};

dayjsWithMomentCompat.unix = (timestamp) => {
  return dayjs.unix(timestamp);
};

dayjsWithMomentCompat.duration = (value, unit) => {
  // Simple duration implementation
  const milliseconds = unit === 'seconds' ? value * 1000 : value;
  return {
    asMilliseconds: () => milliseconds,
    asSeconds: () => milliseconds / 1000,
    asMinutes: () => milliseconds / (1000 * 60),
    asHours: () => milliseconds / (1000 * 60 * 60),
    asDays: () => milliseconds / (1000 * 60 * 60 * 24),
    asWeeks: () => milliseconds / (1000 * 60 * 60 * 24 * 7),
    asMonths: () => milliseconds / (1000 * 60 * 60 * 24 * 30),
    asYears: () => milliseconds / (1000 * 60 * 60 * 24 * 365),
    humanize: () => {
      const seconds = milliseconds / 1000;
      if (seconds < 60) return `${Math.round(seconds)} seconds`;
      if (seconds < 3600) return `${Math.round(seconds / 60)} minutes`;
      if (seconds < 86400) return `${Math.round(seconds / 3600)} hours`;
      if (seconds < 604800) return `${Math.round(seconds / 86400)} days`;
      if (seconds < 2592000) return `${Math.round(seconds / 604800)} weeks`;
      if (seconds < 31536000) return `${Math.round(seconds / 2592000)} months`;
      return `${Math.round(seconds / 31536000)} years`;
    }
  };
};

dayjsWithMomentCompat.now = () => {
  return dayjs().valueOf();
};

dayjsWithMomentCompat.isMoment = (obj) => {
  return dayjs.isDayjs(obj);
};

// Format helpers
dayjsWithMomentCompat.formatDate = (date, format = 'YYYY-MM-DD') => {
  if (!date) return '';
  return dayjs(date).format(format);
};

dayjsWithMomentCompat.formatDateTime = (date, format = 'YYYY-MM-DD HH:mm:ss') => {
  if (!date) return '';
  return dayjs(date).format(format);
};

dayjsWithMomentCompat.fromNow = (date) => {
  if (!date) return '';
  return dayjs(date).fromNow();
};

// Setup global moment compatibility
if (typeof window !== 'undefined') {
  window.moment = dayjsWithMomentCompat;
}

export default dayjsWithMomentCompat;
